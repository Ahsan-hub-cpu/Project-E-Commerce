@extends('layouts.app')

@section('content')

<style>
    .cart-total th, .cart-total td{
        color: green;
        font-weight: bold;
        font-size: 21px !important;
    }
</style>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="shop-checkout container">
        <h2 class="page-title">Shipping and Checkout</h2>
        <div class="checkout-steps">
            <a href="{{ route('cart.index') }}" class="checkout-steps__item active">
                <span class="checkout-steps__item-number">01</span>
                <span class="checkout-steps__item-title">
                    <span>Shopping Bag</span>
                    <em>Manage Your Items List</em>
                </span>
            </a>
            <a href="{{ route('cart.checkout') }}" class="checkout-steps__item active">
                <span class="checkout-steps__item-number">02</span>
                <span class="checkout-steps__item-title">
                    <span>Shipping and Checkout</span>
                    <em>Checkout Your Items List</em>
                </span>
            </a>
            <a href="#" class="checkout-steps__item">
                <span class="checkout-steps__item-number">03</span>
                <span class="checkout-steps__item-title">
                    <span>Confirmation</span>
                    <em>Order Confirmation</em>
                </span>
            </a>
        </div>
        <form name="checkout-form" action="{{ route('cart.place.order') }}" method="POST">
            @csrf
            <div class="checkout-form">
                <div class="billing-info__wrapper">
                    <div class="row">
                        <div class="col-6">
                            <h4>SHIPPING DETAILS</h4> 
                        </div>
                        <div class="col-6">
                            @if($address)  
                                <a href="#" class="btn btn-info btn-sm float-right">Change Address</a> 
                                <a href="#" class="btn btn-warning btn-sm float-right mr-3">Edit Address</a> 
                            @endif
                        </div>
                    </div>   
                    @if($address) 
                        <div class="row">
                            <div class="col-md-12">
                                <div class="my-account__address-list">
                                    <div class="my-account__address-item">                                    
                                        <div class="my-account__address-item__detail">
                                            <p>{{ $address->email }}</p>
                                            <p>{{ $address->name }}</p>
                                            <p>{{ $address->address }}</p>
                                            <p>{{ $address->landmark }}</p>
                                            <p>{{ $address->city }}, {{ $address->state }}, {{ $address->country }}</p>
                                            <p>{{ $address->zip }}</p>
                                            <p>Phone: {{ $address->phone }}</p>                                        
                                        </div>
                                    </div>                                
                                </div>
                            </div>
                        </div>  
                    @else             
                        <div class="row mt-5">
                            <div class="col-md-6">
                                <div class="form-floating my-3">
                                    <input type="text" class="form-control" id="email" name="email" value="{{ old('email') }}">
                                    <label for="email">Email *</label>
                                    <span class="text-danger">@error('email') {{ $message }} @enderror</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating my-3">
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}">
                                    <label for="name">Full Name *</label>
                                    <span class="text-danger">@error('name') {{ $message }} @enderror</span>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-floating my-3">
                                    <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}">
                                    <label for="phone">Phone Number *</label>
                                    <span class="text-danger">@error('phone') {{ $message }} @enderror</span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating my-3">
                                    <input type="text" class="form-control" id="zip" name="zip" value="{{ old('zip') }}">
                                    <label for="zip">Pincode *</label>
                                    <span class="text-danger">@error('zip') {{ $message }} @enderror</span>
                                </div>
                            </div>                        

                            <div class="col-md-4">
                                <div class="form-floating mt-3 mb-3">
                                    <input type="text" class="form-control" id="state" name="state" value="{{ old('state') }}">
                                    <label for="state">State *</label>
                                    <span class="text-danger">@error('state') {{ $message }} @enderror</span>
                                </div>                            
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating my-3">
                                    <input type="text" class="form-control" id="city" name="city" value="{{ old('city') }}">
                                    <label for="city">Town / City *</label>
                                    <span class="text-danger">@error('city') {{ $message }} @enderror</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating my-3">
                                    <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}">
                                    <label for="address">House no, Building Name *</label>
                                    <span class="text-danger">@error('address') {{ $message }} @enderror</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating my-3">
                                    <input type="text" class="form-control" id="locality" name="locality" value="{{ old('locality') }}">
                                    <label for="locality">Road Name, Area, Colony *</label>
                                    <span class="text-danger">@error('locality') {{ $message }} @enderror</span>
                                </div>
                            </div>    

                            <div class="col-md-12">
                                <div class="form-floating my-3">
                                    <input type="text" class="form-control" id="landmark" name="landmark" value="{{ old('landmark') }}">
                                    <label for="landmark">Landmark *</label>
                                    <span class="text-danger">@error('landmark') {{ $message }} @enderror</span>
                                </div>
                            </div>                                         
                        </div> 
                    @endif                   
                </div>
                
                @php
                    // Calculate cart subtotal and shipping cost conditionally.
                    $cartSubtotal = (float) str_replace(',', '', Cart::instance('cart')->subtotal());
                    $shippingCost = ($cartSubtotal > 7000) ? 0 : 250;
                @endphp

                <div class="checkout__totals-wrapper">
                    <div class="sticky-content">
                        <div class="checkout__totals">
                            <h3>Your Order</h3>
                            <table class="checkout-cart-items">
                                <thead>
                                    <tr>
                                        <th>PRODUCT</th>
                                        <th class="text-right">SUBTOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (Cart::instance('cart')->content() as $item)
                                        <tr>
                                            <td>
                                                {{ $item->name }} x {{ $item->qty }}
                                            </td>
                                            <td class="text-right">
                                                PKR {{ $item->subtotal }}
                                            </td>
                                        </tr>
                                    @endforeach                                    
                                </tbody>
                            </table>
                            
                            @if(session()->has('discounts'))
                                <table class="checkout-totals">
                                    <tbody>
                                        <tr>
                                            <th>Subtotal</th>
                                            <td class="text-right">PKR {{ Cart::instance('cart')->subtotal() }}</td>
                                        </tr> 
                                        <tr>
                                            <th>Discount {{ session()->get("coupon")["code"] }}</th>
                                            <td class="text-right">-PKR {{ session()->get("discounts")["discount"] }}</td>
                                        </tr> 
                                        <tr>
                                            <th>Subtotal After Discount</th>
                                            <td class="text-right">PKR {{ session()->get("discounts")["subtotal"] }}</td>
                                        </tr>
                                        <tr>
                                            <th>SHIPPING</th>
                                            <td class="text-right">
                                                @if($shippingCost == 0)
                                                    Free
                                                @else
                                                    PKR {{ number_format($shippingCost, 2) }}
                                                @endif
                                            </td>
                                        </tr>                             
                                        <tr>
                                            <th>VAT</th>
                                            <td class="text-right">PKR {{ session()->get("discounts")["tax"] }}</td>
                                        </tr>
                                        <tr class="cart-total">
                                            <th>Total</th>
                                            <td class="text-right">
                                                PKR {{ number_format((float) str_replace(',', '', session()->get("discounts")["total"]) + $shippingCost, 2) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            @else
                                <table class="checkout-totals">
                                    <tbody>
                                        <tr>
                                            <th>SUBTOTAL</th>
                                            <td class="text-right">PKR {{ Cart::instance('cart')->subtotal() }}</td>
                                        </tr>
                                        <tr>
                                            <th>SHIPPING</th>
                                            <td class="text-right">
                                                @if($shippingCost == 0)
                                                    Free
                                                @else
                                                    PKR {{ number_format($shippingCost, 2) }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>VAT</th>
                                            <td class="text-right">PKR {{ Cart::instance('cart')->tax() }}</td>
                                        </tr>
                                        <tr class="cart-total">
                                            <th>TOTAL</th>
                                            <td class="text-right">
                                                PKR {{ number_format((float) str_replace(',', '', Cart::instance('cart')->total()) + $shippingCost, 2) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            @endif
                        </div>
                        <div class="checkout__payment-methods">
                            <div class="form-check">
                                <input class="form-check-input form-check-input_fill" type="radio" name="mode" value="card" id="mode_1">
                                <label class="form-check-label" for="mode_1">
                                    Debit or Credit Card (Coming Soon)                                 
                                </label>
                            </div> 
                            {{-- <div class="form-check">
                                <input class="form-check-input form-check-input_fill" type="radio" name="mode" value="paypal" id="mode_2">
                                <label class="form-check-label" for="mode_2">
                                    Paypal                                    
                                </label>
                            </div> --}}
                            <div class="form-check">
                                <input class="form-check-input form-check-input_fill" type="radio" name="mode" value="cod" checked id="mode_3">
                                <label class="form-check-label" for="mode_3">
                                    Cash on delivery                                    
                                </label>
                            </div>
                            <div class="policy-text">
                                Your personal data will be used to process your order, support your experience throughout this website, and for other purposes described in our <a href="{{ route('privacy.policy') }}" target="_blank">privacy policy</a>.
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">PLACE ORDER</button>
                    </div>
                </div>
            </div>
        </form>
    </section>
</main>

@endsection
