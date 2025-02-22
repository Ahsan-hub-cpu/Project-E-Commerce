@php
    $cartSubtotal = (float) str_replace(',', '', Cart::instance('cart')->subtotal());
    $shippingCost = ($cartSubtotal > 7000) ? 0 : 250;
@endphp

<table class="cart-totals">
    <tbody>
        @if(Session()->has('discounts'))
            @php
                $discountData = session()->get('discounts');
                $discount = (float) $discountData['discount'];
                $discountedSubtotal = (float) $discountData['subtotal'];
                $discountTax = (float) $discountData['tax'];
                $discountTotal = (float) str_replace(',', '', $discountData['total']);
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
                $cartTotal = (float) str_replace(',', '', Cart::instance('cart')->total());
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
                <td>PKR {{ number_format(Cart::instance('cart')->tax(), 2) }}</td>
            </tr>
            <tr class="cart-total">
                <th>Total</th>
                <td>PKR {{ number_format($finalTotal, 2) }}</td>
            </tr>
        @endif
    </tbody>
</table>
