<table class="cart-totals">
    <tbody>
        @if(Session()->has('discounts'))
            <tr>
                <th>Subtotal</th>
                <td>PKR {{ Cart::instance('cart')->subtotal() }}</td>
            </tr>
            <tr>
                <th>Discount {{ Session()->get("coupon")["code"] }}</th>
                <td>-PKR {{ Session()->get("discounts")["discount"] }}</td>
            </tr>
            <tr>
                <th>Subtotal After Discount</th>
                <td>PKR {{ Session()->get("discounts")["subtotal"] }}</td>
            </tr>
            <tr>
                <th>SHIPPING</th>
                <td class="text-right">Free</td>
            </tr>
            <tr>
                <th>VAT</th>
                <td>PKR {{ Session()->get("discounts")["tax"] }}</td>
            </tr>
            <tr class="cart-total">
                <th>Total</th>
                <td>PKR {{ Session()->get("discounts")["total"] }}</td>
            </tr>
        @else
            <tr>
                <th>Subtotal</th>
                <td>PKR {{ Cart::instance('cart')->subtotal() }}</td>
            </tr>
            <tr>
                <th>SHIPPING</th>
                <td class="text-right">Free</td>
            </tr>
            <tr>
                <th>VAT</th>
                <td>PKR {{ Cart::instance('cart')->tax() }}</td>
            </tr>
            <tr class="cart-total">
                <th>Total</th>
                <td>PKR {{ Cart::instance('cart')->total() }}</td>
            </tr>
        @endif
    </tbody>
</table>