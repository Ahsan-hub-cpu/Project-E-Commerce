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
use Carbon\Carbon;
use Cart;
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
        $request->validate([
            'size_id'  => 'required|exists:sizes,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $size = Sizes::find($request->size_id);
    
        if ($size) {
            $productVariation = Product_Variations::where('product_id', $request->id)
                                                    ->where('size_id', $size->id)
                                                    ->first();
    
            if ($productVariation) {
                if ($request->quantity > $productVariation->quantity) {
                    return redirect()->back()->with('error', 'Sorry, only ' . $productVariation->quantity . ' items are available in stock.');
                }
    
                Cart::instance('cart')->add(
                    $request->id,                         
                    $request->name,                       
                    $request->quantity,                    
                    $request->price,                      
                    [
                        'size'                => $size->name,              // Size Name
                        'size_id'             => $size->id,                // Size ID
                        'product_variation_id'=> $productVariation->id,    // Product Variation ID
                    ]
                )->associate('App\Models\Product'); 
    
                return redirect()->back()->with('success', 'Product added to cart!');
            } else {
                return redirect()->back()->with('error', 'Product variation not found.');
            }
        } else {
            return redirect()->back()->with('error', 'Invalid size or color selection.');
        }
    }
    

    // Increase the quantity of an item in the cart
    public function updateQuantity(Request $request)
    {
        $request->validate([
            'rowId' => 'required',
            'quantity' => 'required|integer|min:1'
        ]);
    
        $cartItem = Cart::instance('cart')->get($request->rowId);
        $product = Product::find($cartItem->id);
    
        if ($product->quantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, only ' . $product->quantity . ' items are available in stock.'
            ]);
        }
    
        Cart::instance('cart')->update($request->rowId, $request->quantity);
        $this->calculateDiscounts();
    
        return response()->json([
            'success' => true,
            'subtotal' => Cart::instance('cart')->get($request->rowId)->subtotal(),
            'totals' => view('partials.cart-totals')->render()
        ]);
    }

    // Remove an item from the cart
    public function remove_item_from_cart($rowId)
    {
        Cart::instance('cart')->remove($rowId);
        return redirect()->back();
    }

    // Empty the entire cart
    public function empty_cart()
    {
        Cart::instance('cart')->destroy();
        return redirect()->back();
    }

    // Apply coupon code
    public function apply_coupon_code(Request $request)
    {        
        $coupon_code = $request->coupon_code;
        if (isset($coupon_code)) {
            $coupon = Coupon::where('code', $coupon_code)
                            ->where('expiry_date', '>=', Carbon::today())
                            ->where('cart_value', '<=', Cart::instance('cart')->subtotal())
                            ->first();

            if (!$coupon) {
                return back()->with('error', 'Invalid coupon code!');
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

    // Calculate discounts and session values
    public function calculateDiscounts()
    {
        $discount = 0;
        if (session()->has('coupon')) {
            if (session()->get('coupon')['type'] == 'fixed') {
                $discount = session()->get('coupon')['value'];
            } else {
                $discount = (Cart::instance('cart')->subtotal() * session()->get('coupon')['value']) / 100;
            }

            $subtotalAfterDiscount = Cart::instance('cart')->subtotal() - $discount;
            $taxAfterDiscount = ($subtotalAfterDiscount * config('cart.tax')) / 100;
            $totalAfterDiscount = $subtotalAfterDiscount + $taxAfterDiscount;

            session()->put('discounts', [
                'discount' => number_format(floatval($discount), 2, '.', ''),
                'subtotal' => number_format(floatval(Cart::instance('cart')->subtotal() - $discount), 2, '.', ''),
                'tax' => number_format(floatval($taxAfterDiscount), 2, '.', ''),
                'total' => number_format(floatval($totalAfterDiscount), 2, '.', '')
            ]);
        }
    }

    // Remove coupon code
    public function remove_coupon_code()
    {
        session()->forget('coupon');
        session()->forget('discounts');
        return back()->with('status', 'Coupon has been removed!');
    }

    public function checkout()
    {
        $address = session()->get('address', null);
        return view('checkout',compact('address'));
    }

    public function setAmountForCheckout()
    { 
        if (Cart::instance('cart')->count() > 0) {
            if (session()->has('coupon')) {
                session()->put('checkout', [
                    'discount' => session()->get('discounts')['discount'],
                    'subtotal' => session()->get('discounts')['subtotal'],
                    'tax' => session()->get('discounts')['tax'],
                    'total' => session()->get('discounts')['total']
                ]);
            } else {
                session()->put('checkout', [
                    'discount' => 0,
                    'subtotal' => Cart::instance('cart')->subtotal(),
                    'tax' => Cart::instance('cart')->tax(),
                    'total' => Cart::instance('cart')->total()
                ]);
            }
        } else {
            session()->forget('checkout');
        }
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

    $subtotal = (float) str_replace(',', '', $checkout['subtotal']);
    $discount = (float) str_replace(',', '', $checkout['discount']);
    $tax      = (float) str_replace(',', '', $checkout['tax']);
    $total    = (float) str_replace(',', '', $checkout['total']);

    try {

        foreach (Cart::instance('cart')->content() as $item) {
            $product = Product::find($item->id);
            if (!$product) {
                return back()->with('error', "Product not found.");
            }
            if ($product->quantity < $item->qty) {
                return back()->with('error', "Sorry, {$product->name} is out of stock.");
            }
        }

        $order = new Order();
        $order->email    = $request->email;
        $order->subtotal = $subtotal;
        $order->discount = $discount;
        $order->tax      = $tax;
        $order->total    = $total;
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

       
        foreach (Cart::instance('cart')->content() as $item) {
            $product = Product::find($item->id);
            $product->quantity -= $item->qty;
            $product->save();

       
            $orderItem = new OrderItem();
            $orderItem->product_id           = $item->id;
            $orderItem->order_id             = $order->id;
            $orderItem->price                = $item->price;
            $orderItem->quantity             = $item->qty;
            $orderItem->product_variation_id = $item->options->product_variation_id ?? null;
            $orderItem->save();
        }

        if ($request->mode === 'cod') {
            $transaction = new Transaction();
            $transaction->order_id = $order->id;
            $transaction->mode = 'cod';
            $transaction->status = 'pending';
            $transaction->save();
        }

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


  
    // public function place_order(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email',
    //         'name' => 'required|max:100',
    //         'phone' => 'required|numeric|digits:10',
    //         'zip' => 'required|numeric|digits:6',
    //         'state' => 'required',
    //         'city' => 'required',
    //         'address' => 'required',
    //         'locality' => 'required',
    //         'landmark' => 'nullable',
    //         'mode' => 'required|in:card,paypal,cod',  
    //     ]);
    //     $this->setAmountForCheckout();
    //     $checkout = session()->get('checkout');
    //     if (!$checkout) {
    //         return redirect()->route('cart.index')->with('error', 'Checkout data is missing.');
    //     }
    
    //     $subtotal = (float) str_replace(',', '', $checkout['subtotal']);
    //     $discount = (float) str_replace(',', '', $checkout['discount']);
    //     $tax = (float) str_replace(',', '', $checkout['tax']);
    //     $total = (float) str_replace(',', '', $checkout['total']);
    
      
    //     $order = new Order();
    //     $order->email = $request->email; 
    //     $order->subtotal = $subtotal;
    //     $order->discount = $discount;
    //     $order->tax = $tax;
    //     $order->total = $total;
    //     $order->name = $request->name;
    //     $order->phone = $request->phone;
    //     $order->locality = $request->locality;
    //     $order->address = $request->address;
    //     $order->city = $request->city;
    //     $order->state = $request->state;
    //     $order->country = 'N/A'; 
    //     $order->landmark = $request->landmark;
    //     $order->zip = $request->zip;
    //     $order->save();
    
    //     foreach (Cart::instance('cart')->content() as $item) {
    //         $orderItem = new OrderItem();
    //         $orderItem->product_id = $item->id;
    //         $orderItem->order_id = $order->id;
    //         $orderItem->price = $item->price;
    //         $orderItem->quantity = $item->qty;
    //         $orderItem->product_variation_id = $item->options->product_variation_id ?? null;
    //         $orderItem->save();
    //     }
    
    //     if ($request->mode === 'cod') {
    //         $transaction = new Transaction();
    //         $transaction->order_id = $order->id;
    //         $transaction->mode = 'cod';
    //         $transaction->status = 'pending';
    //         $transaction->save();
    //     }
    
    //     Cart::instance('cart')->destroy();
    //     session()->forget('checkout');
    //     session()->forget('coupon');
    //     session()->forget('discounts');
    //     session()->put('order_id', $order->id);
    
    //     try {
    //         Log::info('Sending order confirmation email...', ['order' => $order]);
    //         Mail::to($request->email)->send(new OrderConfirmation($order));
    //     } catch (\Exception $e) {
    //         Log::error('Error sending email', ['error' => $e->getMessage()]);
    //         return redirect()->route('cart.confirmation')->with('error', 'There was an issue sending the email.');
    //     }
    
    //     return redirect()->route('cart.confirmation')->with('order_id', $order->id);
    // }
    

    public function confirmation()
    {
        if (session()->has('order_id')) {
            $order = Order::find(session()->get('order_id'));
            return view('order-confirmation', compact('order'));
        }
        return redirect()->route('cart.index');
    }
}
