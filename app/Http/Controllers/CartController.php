<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use App\Models\Sizes;
use App\Models\OrderItem;
use App\Models\Product_Variations;
use App\Models\Product;
use App\Models\Transaction;
use Cart;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmation;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::instance('cart')->content();
        return view('cart', compact('cartItems'));
    }

    public function addToCart(Request $request)
    {
        // Validate that a size is selected and quantity is at least 1.
        $request->validate([
            'size_id'  => 'required|exists:sizes,id',
            'quantity' => 'required|integer|min:1'
        ]);

        // Find the selected size.
        $size = Sizes::find($request->size_id);
        if (!$size) {
            return redirect()->back()->with('error', 'Invalid size selection.');
        }

        // Retrieve the product variation for the given product and size.
        $productVariation = Product_Variations::where('product_id', $request->id)
                                               ->where('size_id', $size->id)
                                               ->first();
        if (!$productVariation) {
            return redirect()->back()->with('error', 'Product variation not found.');
        }

        // Check size-specific stock.
        if ($request->quantity > $productVariation->quantity) {
            return redirect()->back()->with('error', 'Sorry, only ' . $productVariation->quantity . ' items are available in this size.');
        }

        // Retrieve the product and check global stock.
        $product = Product::find($request->id);
        if (!$product) {
            return redirect()->back()->with('error', 'Product not found.');
        }
        if ($request->quantity > $product->quantity) {
            return redirect()->back()->with('error', 'Sorry, only ' . $product->quantity . ' items are available in stock.');
        }

        // Add the product to the cart, passing extra options:
        Cart::instance('cart')->add(
            $request->id,                         // Product ID.
            $request->name,                       // Product Name.
            $request->quantity,                   // Quantity.
            $request->price,                      // Price.
            [
                'size'                => $size->name,               // Size Name.
                'size_id'             => $size->id,                 // Size ID.
                'product_variation_id'=> $productVariation->id,     // Variation ID.
                'available_quantity'  => $productVariation->quantity, // Variation (size-specific) quantity.
                'global_quantity'     => $product->quantity,        // Global product quantity.
            ]
        )->associate('App\Models\Product');

        return redirect()->back()->with('success', 'Product added to cart!');
    }

    public function updateQuantity(Request $request)
    {
        $request->validate([
            'rowId'    => 'required',
            'quantity' => 'required|integer|min:1'
        ]);

        $rowId = $request->rowId;
        $newQuantity = $request->quantity;

        $cartItem = Cart::instance('cart')->get($rowId);
        if (!$cartItem) {
            return response()->json(['success' => false, 'message' => 'Cart item not found.']);
        }

        // Retrieve available and global quantities from cart item options.
        $availableQuantity = isset($cartItem->options->available_quantity)
                             ? (int)$cartItem->options->available_quantity
                             : PHP_INT_MAX;
        $globalQuantity = isset($cartItem->options->global_quantity)
                          ? (int)$cartItem->options->global_quantity
                          : PHP_INT_MAX;
        $allowedMax = min($availableQuantity, $globalQuantity);

        if ($newQuantity > $allowedMax) {
            return response()->json([
                'success' => false,
                'message' => "Only " . $allowedMax . " items are available."
            ]);
        }

        // Update the quantity in the cart.
        Cart::instance('cart')->update($rowId, ['qty' => $newQuantity]);
        
        // Run any discount calculations (if applicable).
        $this->calculateDiscounts();
        
        // Get the updated cart item.
        $updatedCartItem = Cart::instance('cart')->get($rowId);

        // Set the fixed shipping cost.
        $shippingCost = 250; // Fixed shipping cost in PKR

        // Recalculate totals from the cart.
        // These methods should return the totals without any shipping charge.
        $subtotal = (float) str_replace(',', '', Cart::instance('cart')->subtotal());
        $tax      = (float) str_replace(',', '', Cart::instance('cart')->tax());
        $total    = (float) str_replace(',', '', Cart::instance('cart')->total()); // Cart total (without shipping)

        // Add the fixed shipping cost only once.
        $finalTotal = $total + $shippingCost;

        // Render the updated totals partial view with the calculated values.
        // Make sure your partial view uses the $finalTotal variable directly.
        $totals = view('partials.cart-totals', compact('shippingCost', 'subtotal', 'tax', 'finalTotal'))->render();

        return response()->json([
            'success'     => true,
            'newQuantity' => $updatedCartItem->qty,
            'subtotal'    => $updatedCartItem->price * $updatedCartItem->qty,
            'totals'      => $totals,
        ]);
    }

    public function remove_item_from_cart($rowId)
    {
        Cart::instance('cart')->remove($rowId);
        $this->calculateDiscounts();
        return redirect()->back();
    }

    public function empty_cart()
    {
        Cart::instance('cart')->destroy();
        return redirect()->back();
    }

    public function apply_coupon_code(Request $request)
    {
        $coupon_code = $request->coupon_code;

        if (isset($coupon_code)) {
            $cartSubtotal = floatval(str_replace(',', '', Cart::instance('cart')->subtotal()));
            $coupon = Coupon::where('code', $coupon_code)
                            ->where('expiry_date', '>=', Carbon::today())
                            ->where('cart_value', '<=', $cartSubtotal)
                            ->first();
            if (!$coupon) {
                session()->forget('coupon');
                return back()->with('error', 'Invalid coupon code');
            }

            session()->put('coupon', [
                'code' => $coupon->code,
                'type' => $coupon->type,
                'value' => $coupon->value,
                'cart_value' => $coupon->cart_value
            ]);

            $this->calculateDiscounts();

            return back()->with('status', 'Coupon code has been applied!');
        } else {
            return back()->with('error', 'Invalid coupon code!');
        }
    }

    public function calculateDiscounts()
    {
        $discount = 0;

        if (session()->has('coupon')) {
            $subtotal = floatval(str_replace(',', '', Cart::instance('cart')->subtotal()));
            $couponValue = floatval(session()->get('coupon')['value']);
            $couponType = session()->get('coupon')['type'];
            $cartValue = floatval(session()->get('coupon')['cart_value']);

            if ($subtotal >= $cartValue) {
                if ($couponType == 'fixed') {
                    $discount = $couponValue;
                } else {
                    $discount = ($subtotal * $couponValue) / 100;
                }

                $subtotalAfterDiscount = $subtotal - $discount;
                $taxAfterDiscount = ($subtotalAfterDiscount * config('cart.tax')) / 100;
                $totalAfterDiscount = $subtotalAfterDiscount + $taxAfterDiscount;

                session()->put('discounts', [
                    'discount' => number_format($discount, 2, '.', ''),
                    'subtotal' => number_format($subtotalAfterDiscount, 2, '.', ''),
                    'tax' => number_format($taxAfterDiscount, 2, '.', ''),
                    'total' => number_format($totalAfterDiscount, 2, '.', '')
                ]);
            } else {
                session()->forget('coupon');
                session()->forget('discounts');
            }
        }
    }

    // In setAmountForCheckout, add shipping cost to the total.
    public function setAmountForCheckout()
    {
        $shippingCost = 250; // Fixed shipping cost
        if (Cart::instance('cart')->count() > 0) {
            if (session()->has('coupon')) {
                // Get discount totals, remove commas, then add shipping.
                $discountTotal = (float) str_replace(',', '', session()->get('discounts')['total']);
                $finalTotal = $discountTotal + $shippingCost;
                session()->put('checkout', [
                    'discount' => session()->get('discounts')['discount'],
                    'subtotal' => session()->get('discounts')['subtotal'],
                    'tax' => session()->get('discounts')['tax'],
                    'total' => number_format($finalTotal, 2, '.', '')
                ]);
            } else {
                $cartTotal = (float) str_replace(',', '', Cart::instance('cart')->total());
                $finalTotal = $cartTotal + $shippingCost;
                session()->put('checkout', [
                    'discount' => 0,
                    'subtotal' => Cart::instance('cart')->subtotal(),
                    'tax' => Cart::instance('cart')->tax(),
                    'total' => number_format($finalTotal, 2, '.', '')
                ]);
            }
        } else {
            session()->forget('checkout');
        }
    }

    public function remove_coupon_code()
    {
        session()->forget('coupon');
        session()->forget('discounts');
        return back()->with('status', 'Coupon has been removed!');
    }

    public function checkout()
    {
        $address = session()->get('address', null);
        return view('checkout', compact('address'));
    }

    public function place_order(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'name'     => 'required|max:100',
            'phone'    => 'required|numeric|digits:10',
            'zip'      => 'required|numeric|digits:6',
            'state'    => 'required',
            'city'     => 'required',
            'address'  => 'required',
            'locality' => 'required',
            'landmark' => 'nullable',
            'mode'     => 'required|in:card,paypal,cod',
        ]);

        $this->setAmountForCheckout();
        $checkout = session()->get('checkout');

        if (!$checkout) {
            return redirect()->route('cart.index')->with('error', 'Checkout data is missing.');
        }

        // Remove commas and convert to float.
        $subtotal = (float) str_replace(',', '', $checkout['subtotal']);
        $discount = (float) str_replace(',', '', $checkout['discount']);
        $tax      = (float) str_replace(',', '', $checkout['tax']);
        $total    = (float) str_replace(',', '', $checkout['total']); // This total now includes shipping

        try {
            // Check stock availability for each cart item
            foreach (Cart::instance('cart')->content() as $item) {
                $product = Product::find($item->id);
                if (!$product) {
                    return back()->with('error', "Product not found.");
                }

                // Check if the selected size is in stock
                $productVariation = Product_Variations::where('product_id', $item->id)
                                                      ->where('size_id', $item->options->size_id)
                                                      ->first();
                if (!$productVariation || $productVariation->quantity < $item->qty) {
                    return back()->with('error', "Sorry, the selected size is out of stock.");
                }

                // Check global stock (Product level)
                if ($product->quantity < $item->qty) {
                    return back()->with('error', "Sorry, only " . $product->quantity . " items are available in stock.");
                }
            }

            $shippingCost = 250;

            // Create the order
            $order = new Order();
            $order->email    = $request->email;
            $order->subtotal = $subtotal;
            $order->discount = $discount;
            $order->tax      = $tax;
            $order->total    = $total; // This total includes shipping
            $order->shipping_cost = $shippingCost;
            $order->name     = $request->name;
            $order->phone    = $request->phone;
            $order->locality = $request->locality;
            $order->address  = $request->address;
            $order->city     = $request->city;
            $order->state    = $request->state;
            $order->country  = 'N/A';
            $order->landmark = $request->landmark;
            $order->zip      = $request->zip;
            $order->save();

            // Process order items and reduce stock (both size-specific and global quantities)
            foreach (Cart::instance('cart')->content() as $item) {
                $product = Product::find($item->id);
                $productVariation = Product_Variations::where('product_id', $item->id)
                                                      ->where('size_id', $item->options->size_id)
                                                      ->first();

                // Reduce size-specific stock
                if ($productVariation) {
                    $productVariation->quantity -= $item->qty;
                    $productVariation->save();
                }

                // Reduce global product stock
                $product->quantity -= $item->qty;
                $product->save();

                // Create order item
                $orderItem = new OrderItem();
                $orderItem->product_id           = $item->id;
                $orderItem->order_id             = $order->id;
                $orderItem->price                = $item->price;
                $orderItem->quantity             = $item->qty;
                $orderItem->product_variation_id = $item->options->product_variation_id ?? null;
                $orderItem->save();
            }

            // Handle payment transaction if Cash on Delivery
            if ($request->mode === 'cod') {
                $transaction = new Transaction();
                $transaction->order_id = $order->id;
                $transaction->mode = 'cod';
                $transaction->status = 'pending';
                $transaction->save();
            }

            // Clear cart and session data
            Cart::instance('cart')->destroy();
            session()->forget(['checkout', 'coupon', 'discounts']);
            session()->put('order_id', $order->id);

            try {
                Mail::to($request->email)->send(new OrderConfirmation($order));
            } catch (\Exception $e) {
                Log::error('Error sending email', ['error' => $e->getMessage()]);
                return redirect()->route('cart.confirmation')->with('error', 'Issue sending confirmation email.');
            }

            return redirect()->route('cart.confirmation')->with('order_id', $order->id);

        } catch (\Exception $e) {
            Log::error('Order placement failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Order could not be placed. Please try again.');
        }
    }

    public function confirmation()
    {
        if (session()->has('order_id')) {
            $order = Order::find(session()->get('order_id'));
            return view('order-confirmation', compact('order'));
        }
        return redirect()->route('cart.index');
    }
}
