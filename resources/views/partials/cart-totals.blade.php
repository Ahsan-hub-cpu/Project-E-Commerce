<table class="cart-totals">
    <tbody>
        @php
            $shippingCost = 250; // Fixed shipping cost

            // Convert cart totals to floats by removing commas
            $cartSubtotal = (float) str_replace(',', '', Cart::instance('cart')->subtotal());
            $cartTax = (float) str_replace(',', '', Cart::instance('cart')->tax());
            $cartTotal = (float) str_replace(',', '', Cart::instance('cart')->total());
        @endphp

        @if(Session()->has('discounts'))
            @php
                // Get discount data and convert to floats
                $discountData = session()->get('discounts');
                $discount = (float) $discountData['discount'];
                $discountedSubtotal = (float) $discountData['subtotal'];
                $discountTax = (float) $discountData['tax'];
                $discountTotal = (float) $discountData['total']; // Total after discount (without shipping)
                
                // Add shipping cost only once
                $finalTotal = $discountTotal + $shippingCost;
            @endphp
            <tr>
                <th>Subtotal</th>
                <td>PKR {{ number_format($cartSubtotal, 2) }}</td>
            </tr>
            <tr>
                <th>Discount {{ Session::get("coupon")["code"] }}</th>
                <td>-PKR {{ number_format($discount, 2) }}</td>
            </tr>
            <tr>
                <th>Subtotal After Discount</th>
                <td>PKR {{ number_format($discountedSubtotal, 2) }}</td>
            </tr>
            <tr>
                <th>SHIPPING</th>
                <td class="text-right">PKR {{ number_format($shippingCost, 2) }}</td>
            </tr>
            <tr>
                <th>VAT</th>
                <td>PKR {{ number_format($discountTax, 2) }}</td>
            </tr>
            <tr class="cart-total">
                <th>Total</th>
                <td>PKR {{ number_format($finalTotal, 2) }}</td>
            </tr>
        @else
            @php
                $finalTotal = $cartTotal + $shippingCost;
            @endphp
            <tr>
                <th>Subtotal</th>
                <td>PKR {{ number_format($cartSubtotal, 2) }}</td>
            </tr>
            <tr>
                <th>SHIPPING</th>
                <td class="text-right">PKR {{ number_format($shippingCost, 2) }}</td>
            </tr>
            <tr>
                <th>VAT</th>
                <td>PKR {{ number_format($cartTax, 2) }}</td>
            </tr>
            <tr class="cart-total">
                <th>Total</th>
                <td>PKR {{ number_format($finalTotal, 2) }}</td>
            </tr>
        @endif
    </tbody>
</table>
