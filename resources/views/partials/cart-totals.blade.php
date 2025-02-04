<table class="cart-totals">
    <tbody>
        <tr>
            <th>Subtotal</th>
            <td>PKR {{ Cart::instance('cart')->subtotal() }}</td>
        </tr>
        
        @if(Session()->has('discounts'))
            <tr>
                <th>Discount {{ Session()->get("coupon")["code"] }}</th>
                <td>-PKR {{ Session()->get("discounts")["discount"] }}</td>
            </tr>
            <tr>
                <th>Subtotal After Discount</th>
                <td>PKR {{ Session()->get("discounts")["subtotal"] }}</td>
            </tr>
        @else
            <tr>
                <th>Discount</th>
                <td>-PKR 0</td>
            </tr>
        @endif
        
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
    </tbody>
</table>
