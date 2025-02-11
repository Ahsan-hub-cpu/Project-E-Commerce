@extends('layouts.app')
@section('content')
<style>
    .cart-totals td {
        text-align: right;
    }
    .cart-total th, .cart-total td {
        color: green;
        font-weight: bold;
        font-size: 21px !important;
    }
    .text-success {
        color: #42ff00 !important;
    }
    .text-danger {
        color: #ee1907 !important;
    }
    /* Optional: Style for disabled button */
    .qty-control__increase:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>
<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="shop-checkout container">
        <h2 class="page-title">Cart</h2>
        <div class="checkout-steps">
            <a href="javascript:void(0);" class="checkout-steps__item active">
                <span class="checkout-steps__item-number">01</span>
                <span class="checkout-steps__item-title">
                    <span>Shopping Bag</span>
                    <em>Manage Your Items List</em>
                </span>
            </a>
            <a href="javascript:void(0);" class="checkout-steps__item">
                <span class="checkout-steps__item-number">02</span>
                <span class="checkout-steps__item-title">
                    <span>Shipping and Checkout</span>
                    <em>Checkout Your Items List</em>
                </span>
            </a>
            <a href="javascript:void(0);" class="checkout-steps__item">
                <span class="checkout-steps__item-number">03</span>
                <span class="checkout-steps__item-title">
                    <span>Confirmation</span>
                    <em>Order Confirmation</em>
                </span>
            </a>
        </div>
        <div class="shopping-cart">
            @if($cartItems->count() > 0)
                <div class="cart-table__wrapper">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th></th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        @php
                            // Set the fixed shipping cost once.
                            $shippingCost = 250;
                        @endphp
                            @foreach ($cartItems as $cartItem)
                                <tr>
                                    <td>
                                        <div class="shopping-cart__product-item">
                                            <img loading="lazy" src="{{ asset('uploads/products/thumbnails/' . $cartItem->model->image) }}" width="120" height="120" alt="" />
                                        </div>
                                    </td>
                                    <td>
                                        <div class="shopping-cart__product-item__detail">
                                            <h4>{{ $cartItem->name }}</h4>
                                            <ul class="shopping-cart__product-item__options">
                                                <li>
                                                    Size:
                                                    @if(isset($cartItem->model->availableSizes))
                                                        <select class="size-select" data-rowid="{{ $cartItem->rowId }}">
                                                            @foreach($cartItem->model->availableSizes as $size => $availableQty)
                                                                <option value="{{ $size }}"
                                                                    data-quantity="{{ $availableQty }}"
                                                                    {{ (strtolower($cartItem->options['size'] ?? '') == strtolower($size)) ? 'selected' : '' }}>
                                                                    {{ strtoupper($size) }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        {{ strtoupper($cartItem->options['size'] ?? 'N/A') }}
                                                    @endif
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="shopping-cart__product-price">PKR {{ $cartItem->price }}</span>
                                    </td>
                                    <td>
                                        <div class="qty-control position-relative">
                                            <input type="number" name="quantity" 
                                                   value="{{ $cartItem->qty }}" 
                                                   min="1" 
                                                   class="qty-control__number text-center" 
                                                   data-rowid="{{ $cartItem->rowId }}"
                                                   data-max="{{ $cartItem->options->available_quantity }}"
                                                   data-global="{{ $cartItem->options->global_quantity }}">
                                            <button class="qty-control__reduce" data-action="reduce" data-rowid="{{ $cartItem->rowId }}">-</button>
                                            <button class="qty-control__increase" data-action="increase" data-rowid="{{ $cartItem->rowId }}">+</button>
                                        </div>
                                        <span class="text-danger stock-error" id="stock-error-{{ $cartItem->rowId }}"></span>
                                    </td>
                                    <td>
                                        <span class="shopping-cart__subtotal" id="subtotal-{{ $cartItem->rowId }}">PKR {{ $cartItem->subtotal() }}</span>
                                    </td>
                                    <td>
                                        <form method="POST" action="{{ route('cart.remove', ['rowId' => $cartItem->rowId]) }}">
                                            @csrf
                                            @method("DELETE")
                                            <button type="submit" class="remove-cart">
                                                <svg width="10" height="10" viewBox="0 0 10 10" fill="#767676" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M0.259435 8.85506L9.11449 0L10 0.885506L1.14494 9.74056L0.259435 8.85506Z" />
                                                    <path d="M0.885506 0.0889838L9.74057 8.94404L8.85506 9.82955L0 0.97449L0.885506 0.0889838Z" />
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="cart-table-footer">
                        @if(!Session()->has("coupon"))
                            <form class="position-relative bg-body" method="POST" action="{{ route('cart.coupon.apply') }}">
                                @csrf
                                <input class="form-control" type="text" name="coupon_code" placeholder="Coupon Code">
                                <input class="btn-link fw-medium position-absolute top-0 end-0 h-100 px-4" type="submit" value="APPLY COUPON">
                            </form>
                        @else
                            <form class="position-relative bg-body" method="POST" action="{{ route('cart.coupon.remove') }}">
                                @csrf
                                @method('DELETE')
                                <input class="form-control text-success fw-bold" type="text" name="coupon_code" placeholder="Coupon Code" value="{{ session()->get('coupon')['code'] }} Applied!" readonly>
                                <input class="btn-link fw-medium position-absolute top-0 end-0 h-100 px-4 text-danger" type="submit" value="REMOVE COUPON">
                            </form>
                        @endif
                        <form class="position-relative bg-body" method="POST" action="{{ route('cart.empty') }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-light" type="submit">CLEAR CART</button>
                        </form>
                    </div>
                    <div>
                        @if(Session()->has('success'))
                            <p class="text-success">{{ session()->get('success') }}</p>
                        @elseif(Session()->has('error'))
                            <p class="text-danger">{{ session()->get('error') }}</p>
                        @endif
                    </div>
                </div>
                <div class="shopping-cart__totals-wrapper">
                    <div class="sticky-content">
                        <div class="shopping-cart__totals">
                            <h3>Cart Totals</h3>
                            @if(Session()->has('discounts'))
                                <table class="cart-totals">
                                    <tbody>
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
                                            <td class="text-right">PKR {{ number_format($shippingCost, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>VAT</th>
                                            <td>PKR {{ Session()->get("discounts")["tax"] }}</td>
                                        </tr>
                                        <tr class="cart-total">
                                            @php
                                                // Remove commas before arithmetic
                                                $discountTotal = (float) str_replace(',', '', Session::get("discounts")["total"]);
                                                $finalTotal = $discountTotal + $shippingCost;
                                            @endphp
                                            <th>Total</th>
                                            <td>PKR {{ number_format($finalTotal, 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            @else
                                <table class="cart-totals">
                                    <tbody>
                                        <tr>
                                            <th>Subtotal</th>
                                            <td>PKR {{ Cart::instance('cart')->subtotal() }}</td>
                                        </tr>
                                        <tr>
                                            <th>SHIPPING</th>
                                            <td class="text-right">PKR {{ number_format($shippingCost, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>VAT</th>
                                            <td>PKR {{ Cart::instance('cart')->tax() }}</td>
                                        </tr>
                                        <tr class="cart-total">
                                            @php
                                                $cartTotal = (float) str_replace(',', '', Cart::instance('cart')->total());
                                                $finalTotal = $cartTotal + $shippingCost;
                                            @endphp
                                            <th>Total</th>
                                            <td>PKR {{ number_format($finalTotal, 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            @endif
                        </div>
                        <div class="mobile_fixed-btn_wrapper">
                            <div class="button-wrapper container">
                                <button type="button" class="btn btn-primary btn-checkout">PROCEED TO CHECKOUT</button>
                                <div id="checkout-error" class="text-danger"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col-md-12 text-center pt-5 pb-5">
                        <p>No item found in your cart</p>
                        <a href="{{ route('shop.index') }}" class="btn btn-info">Shop Now</a>
                    </div>
                </div>
            @endif
        </div>
    </section>
</main>
@endsection

@push('scripts')
<script>
$(document).ready(function(){

    // Initialize quantity inputs
    $('input.qty-control__number').each(function(){
        var $input = $(this);
        var availableQuantity = parseInt($input.data('max'));
        var globalQuantity = parseInt($input.data('global'));
        var allowedMax = Math.min(availableQuantity, globalQuantity);
        $input.attr('max', allowedMax);
        var currentVal = parseInt($input.val());
        if(currentVal > allowedMax) {
            currentVal = allowedMax;
            $input.val(allowedMax);
        }
        $input.data('lastValid', currentVal);
        var rowId = $input.data('rowid');
        updateButtonState(rowId);
    });

    // Update increase button state based on current quantity.
    function updateButtonState(rowId) {
        var $input = $('input.qty-control__number[data-rowid="'+ rowId +'"]');
        var maxVal = parseInt($input.attr('max'));
        var currentVal = parseInt($input.val());
        var $increaseButton = $('.qty-control__increase[data-rowid="'+ rowId +'"]');
        var $error = $('#stock-error-' + rowId);
        if(currentVal >= maxVal){
            $increaseButton.prop('disabled', true);
            $error.text("Only " + maxVal + " items are available.");
        } else {
            $increaseButton.prop('disabled', false);
            $error.text('');
        }
    }

    // When quantity is manually changed.
    $('input.qty-control__number').on('change', function(){
        var $input = $(this);
        var rowId = $input.data('rowid');
        var inputVal = parseInt($input.val());
        var maxVal = parseInt($input.attr('max'));
        var $error = $('#stock-error-' + rowId);
        if(isNaN(inputVal) || inputVal < 1) {
            inputVal = 1;
            $input.val(1);
            $error.text('');
        }
        if(inputVal > maxVal) {
            inputVal = maxVal;
            $input.val(maxVal);
            $error.text("Only " + maxVal + " items are available.");
        } else if(inputVal === maxVal) {
            $error.text("Only " + maxVal + " items are available.");
        } else {
            $error.text('');
        }
        $input.data('lastValid', inputVal);
        updateQuantity(rowId, inputVal);
        updateButtonState(rowId);
    });

    // When the increase button is clicked.
    $('.qty-control__increase').on('click', function(){
        var rowId = $(this).data('rowid');
        var $input = $('input.qty-control__number[data-rowid="'+ rowId +'"]');
        var lastValid = parseInt($input.data('lastValid'));
        var maxVal = parseInt($input.attr('max'));
        var $error = $('#stock-error-' + rowId);
        if(lastValid < maxVal) {
            var newQuantity = lastValid + 1;
            $error.text('');
            updateQuantity(rowId, newQuantity);
        } else {
            $error.text("Only " + maxVal + " items are available.");
        }
        updateButtonState(rowId);
    });

    // When the reduce button is clicked.
    $('.qty-control__reduce').on('click', function(){
        var rowId = $(this).data('rowid');
        var $input = $('input.qty-control__number[data-rowid="'+ rowId +'"]');
        var lastValid = parseInt($input.data('lastValid'));
        var $error = $('#stock-error-' + rowId);
        if(lastValid > 1) {
            var newQuantity = lastValid - 1;
            $error.text('');
            updateQuantity(rowId, newQuantity);
        }
        updateButtonState(rowId);
    });

    // Checkout redirection.
    $('.shopping-cart .btn-checkout').off('click').on('click', function() {
        window.location.href = "{{ route('cart.checkout') }}";
    });

    // AJAX function to update quantity.
    function updateQuantity(rowId, quantity) {
        $.ajax({
            url: "{{ route('cart.update.quantity') }}",
            method: 'PUT',
            data: {
                _token: "{{ csrf_token() }}",
                rowId: rowId,
                quantity: quantity
            },
            success: function(response) {
                if(response.success) {
                    var $input = $('input.qty-control__number[data-rowid="'+ rowId +'"]');
                    $input.val(response.newQuantity);
                    $input.data('lastValid', response.newQuantity);
                    $('#subtotal-' + rowId).text('PKR ' + response.subtotal);
                    // Replace the totals HTML with the response from the server.
                    $('.cart-totals').html(response.totals);
                    $('#stock-error-' + rowId).text('');
                    var maxVal = parseInt($input.attr('max'));
                    if(response.newQuantity >= maxVal) {
                        $('#stock-error-' + rowId).text("Only " + maxVal + " items are available.");
                    }
                    updateButtonState(rowId);
                } else {
                    $('#stock-error-' + rowId).text(response.message);
                }
            },
            error: function(xhr) {
                $('#checkout-error').text('An error occurred. Please try again.');
            }
        });
    }
});
</script>
@endpush
