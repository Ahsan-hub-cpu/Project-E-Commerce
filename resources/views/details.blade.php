@extends('layouts.app')
@section('content')
<style>
  .filled-heart {
    color: orange;
  }
  /* Style for sold-out labels */
  .sold-out-label {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background: #ff9800;
    color: #fff;
    font-size: 0.9rem;
    font-weight: bold;
    padding: 4px 8px;
    border-radius: 5px;
    z-index: 10;
  }
  .pc__sold-out {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(0,0,0,0.7);
    color: #fff;
    padding: 5px 10px;
    font-weight: bold;
    border-radius: 3px;
    z-index: 10;
  }
  /* Style for quantity error message */
  .qty-error {
    color: red;
    font-size: 0.8rem;
    margin-top: 4px;
  }
</style>

<main class="pt-90">
  <div class="mb-md-1 pb-md-3"></div>
  <section class="product-single container">
    <div class="row">
      <div class="col-lg-7">
        <div class="product-single__media" data-media-type="vertical-thumbnail">
          <div class="product-single__image">
            <div class="swiper-container">
              <div class="swiper-wrapper">
                <div class="swiper-slide product-single__image-item">
                  <img loading="lazy" class="h-auto" src="{{ asset('uploads/products/' . $product->image) }}" width="674" height="674" alt="{{ $product->name }}" />
                  <a data-fancybox="gallery" href="{{ asset('uploads/products/' . $product->image) }}" data-bs-toggle="tooltip" data-bs-placement="left" title="Zoom">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <use href="#icon_zoom" />
                    </svg>
                  </a>
                </div>
                @foreach(explode(',', $product->images) as $gimg)
                  <div class="swiper-slide product-single__image-item">
                    <img loading="lazy" class="h-auto" src="{{ asset('uploads/products/' . $gimg) }}" width="674" height="674" alt="{{ $product->name }}" />
                    <a data-fancybox="gallery" href="{{ asset('uploads/products/' . $gimg) }}" data-bs-toggle="tooltip" data-bs-placement="left" title="Zoom">
                      <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <use href="#icon_zoom" />
                      </svg>
                    </a>
                  </div>
                @endforeach
              </div>
              <div class="swiper-button-prev">
                <svg width="7" height="11" viewBox="0 0 7 11" xmlns="http://www.w3.org/2000/svg">
                  <use href="#icon_prev_sm" />
                </svg>
              </div>
              <div class="swiper-button-next">
                <svg width="7" height="11" viewBox="0 0 7 11" xmlns="http://www.w3.org/2000/svg">
                  <use href="#icon_next_sm" />
                </svg>
              </div>
            </div>
          </div>
          <div class="product-single__thumbnail">
            <div class="swiper-container">
              <div class="swiper-wrapper">
                <div class="swiper-slide product-single__image-item">
                  <img loading="lazy" class="h-auto" src="{{ asset('uploads/products/thumbnails/' . $product->image) }}" width="104" height="104" alt="{{ $product->name }}" />
                </div>
                @foreach(explode(',', $product->images) as $gimg)
                  <div class="swiper-slide product-single__image-item">
                    <img loading="lazy" class="h-auto" src="{{ asset('uploads/products/' . $gimg) }}" width="104" height="104" alt="{{ $product->name }}" />
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-5">
        <div class="d-flex justify-content-between mb-4 pb-md-2">
          <div class="breadcrumb mb-0 d-none d-md-block flex-grow-1">
            <a href="#" class="menu-link menu-link_us-s text-uppercase fw-medium">Home</a>
            <span class="breadcrumb-separator menu-link fw-medium ps-1 pe-1">/</span>
            <a href="#" class="menu-link menu-link_us-s text-uppercase fw-medium">The Shop</a>
          </div>
          <div class="product-single__prev-next d-flex align-items-center justify-content-between justify-content-md-end flex-grow-1">
          </div>
        </div>
        <h1 class="product-single__name">"{{ $product->name }}"</h1>
        <div class="product-single__rating">
          <!-- Add rating display if available -->
        </div>
        <div class="product-single__price">
          <span class="current-price">
            @if($product->sale_price)
              <s>PKR {{ $product->regular_price }}</s> PKR {{ $product->sale_price }}
            @else
              PKR {{ $product->regular_price }}
            @endif
          </span>
        </div>
        <div class="product-single__short-desc">
          <p>{{ $product->short_description }}</p>
        </div>
        <!-- Out-of-Stock Check -->
        @if($product->quantity <= 0)
          <span class="btn btn-secondary mb-3">Sold Out</span>
        @else
          @if(Cart::instance("cart")->content()->where('id', $product->id)->count() > 0)
            <a href="{{ route('cart.index') }}" class="btn btn-warning mb-3">Go to Cart</a>
          @else
            <form name="addtocart-form" method="POST" action="{{ route('cart.add') }}">
              @csrf
              <div class="product-single__options">
                <label for="size">Size:</label>
                <select name="size_id" id="size" class="form-control" required>
                  <option value="">Select Size</option>
                  @foreach($product->sizes as $size)
                    <option value="{{ $size->id }}">{{ $size->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="product-single__addtocart">
                <div class="qty-control position-relative">
                  <!-- The input has a max attribute based on available quantity -->
                  <input type="number" name="quantity" value="1" min="1" max="{{ $product->quantity }}" class="qty-control__number text-center">
                  <div class="qty-control__reduce">-</div>
                  <div class="qty-control__increase">+</div>
                </div><!-- .qty-control -->
                <!-- Container for error message -->
                <div class="qty-error"></div>
                <input type="hidden" name="id" value="{{ $product->id }}" />
                <input type="hidden" name="name" value="{{ $product->name }}" />
                <input type="hidden" name="price" value="{{ $product->sale_price == '' ? $product->regular_price : $product->sale_price }}" />
                <button type="submit" class="btn btn-primary btn-addtocart" data-aside="cartDrawer">Add to Cart</button>
              </div>
            </form>
          @endif
        @endif
        <div class="product-single__addtolinks">
          @if(Cart::instance("wishlist")->content()->where('id', $product->id)->count() > 0)
            <form method="POST" action="{{ route('wishlist.remove', ['rowId' => Cart::instance('wishlist')->content()->where('id', $product->id)->first()->rowId]) }}" id="from">
              @csrf
              @method('DELETE')
              <a href="javascript:void(0)" class="menu-link menu-link_us-s add-to-wishlist filled-heart" onclick="document.getElementById('from').submit();">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <use href="#icon_heart" />
                </svg>
                <span>Remove from Wishlist</span>
              </a>
            </form>
          @else
            <form method="POST" action="{{ route('wishlist.add') }}" id="wishlist-form">
              @csrf
              <input type="hidden" name="id" value="{{ $product->id }}" />
              <input type="hidden" name="name" value="{{ $product->name }}" />
              <input type="hidden" name="quantity" value="1"/>
              <input type="hidden" name="price" value="{{ $product->sale_price == '' ? $product->regular_price : $product->sale_price }}" />
              <a href="javascript:void(0)" class="menu-link menu-link_us-s add-to-wishlist" onclick="document.getElementById('wishlist-form').submit()">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <use href="#icon_heart" />
                </svg>
                <span>Add to Wishlist</span>
              </a>
            </form>
          @endif
          <share-button class="share-button"></share-button>
          <div class="product-single__meta-info">
            <div class="meta-item">
              <label>SKU:</label>
              <span>{{ $product->SKU }}</span>
            </div>
            <div class="meta-item">
              <label>Categories:</label>
              <span>{{ $product->category->name }}</span>
            </div>
            <div class="meta-item">
              <label>Tags:</label>
              <span>N/A</span>
            </div>
          </div>
        </div>
      </div>
      <div class="product-single__details-tab">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
          <li class="nav-item" role="presentation">
            <a class="nav-link nav-link_underscore active" id="tab-description-tab" data-bs-toggle="tab" href="#tab-description" role="tab" aria-controls="tab-description" aria-selected="true">Description</a>
          </li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane fade show active" id="tab-description" role="tabpanel" aria-labelledby="tab-description-tab">
            <div class="product-single__description">
              {{ $product->description }}
            </div>
          </div>
          <!-- Additional tabs can be added here -->
        </div>
      </div>
    </div>
  </section>
  <section class="products-carousel container">
    <h2 class="h3 text-uppercase mb-4 pb-xl-2 mb-xl-4">Related <strong>Products</strong></h2>
    <div id="related_products" class="position-relative">
      <div class="swiper-container js-swiper-slider" data-settings='{
            "autoplay": false,
            "slidesPerView": 4,
            "slidesPerGroup": 4,
            "effect": "none",
            "loop": true,
            "pagination": {
              "el": "#related_products .products-pagination",
              "type": "bullets",
              "clickable": true
            },
            "navigation": {
              "nextEl": "#related_products .products-carousel__next",
              "prevEl": "#related_products .products-carousel__prev"
            },
            "breakpoints": {
              "320": {
                "slidesPerView": 2,
                "slidesPerGroup": 2,
                "spaceBetween": 14
              },
              "768": {
                "slidesPerView": 3,
                "slidesPerGroup": 3,
                "spaceBetween": 24
              },
              "992": {
                "slidesPerView": 4,
                "slidesPerGroup": 4,
                "spaceBetween": 30
              }
            }
          }'>
        <div class="swiper-wrapper">
          @foreach($rproducts as $rproduct)
            <div class="swiper-slide product-card" style="position: relative;">
              <div class="pc__img-wrapper">
                <a href="{{ route('shop.product.details', ['product_slug' => $rproduct->slug]) }}">
                  <img loading="lazy" src="{{ asset('uploads/products/' . $rproduct->image) }}" width="330" height="400" alt="{{ $rproduct->name }}" class="pc__img">
                  @foreach(explode(',', $rproduct->images) as $gimg)
                    <img loading="lazy" src="{{ asset('uploads/products/' . $gimg) }}" width="330" height="400" alt="{{ $rproduct->name }}" class="pc__img pc__img-second">
                  @endforeach
                </a>
                @if($rproduct->quantity <= 0)
                  <div class="sold-out-label">Sold Out</div>
                @endif
                @if($rproduct->quantity > 0)
                  @if(Cart::instance("cart")->content()->where('id', $rproduct->id)->count() > 0)
                    <a href="{{ route('cart.index') }}" class="pc__atc btn anim_appear-bottom btn position-absolute border-0 text-uppercase fw-medium btn-warning mb-3">Go to Cart</a>
                  @else
                    <form name="addtocart-form" method="POST" action="{{ route('cart.add') }}">
                      @csrf
                      <input type="hidden" name="id" value="{{ $rproduct->id }}" />
                      <input type="hidden" name="name" value="{{ $rproduct->name }}" />
                      <input type="hidden" name="quantity" value="1"/>
                      <input type="hidden" name="price" value="{{ $rproduct->sale_price == '' ? $rproduct->regular_price : $rproduct->sale_price }}" />
                    </form>
                  @endif
                @endif
              </div>
              <div class="pc__info position-relative">
                <p class="pc__category">{{ $rproduct->category->name }}</p>
                <h6 class="pc__title">
                  <a href="{{ route('shop.product.details', ['product_slug' => $rproduct->slug]) }}">{{ $rproduct->name }}</a>
                </h6>
                <div class="product-card__price d-flex">
                  <span class="money price">
                    @if($rproduct->sale_price)
                      <s>PKR {{ $rproduct->regular_price }}</s> PKR {{ $rproduct->sale_price }}
                    @else
                      PKR {{ $rproduct->regular_price }}
                    @endif
                  </span>
                </div>
                <button class="pc__btn-wl position-absolute top-0 end-0 bg-transparent border-0 js-add-wishlist" title="Add To Wishlist">
                  <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <use href="#icon_heart" />
                  </svg>
                </button>
              </div>
            </div>
          @endforeach
        </div>
      </div>

      <div class="products-carousel__prev position-absolute top-50 d-flex align-items-center justify-content-center">
        <svg width="25" height="25" viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg">
          <use href="#icon_prev_md" />
        </svg>
      </div>
      <div class="products-carousel__next position-absolute top-50 d-flex align-items-center justify-content-center">
        <svg width="25" height="25" viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg">
          <use href="#icon_next_md" />
        </svg>
      </div>

      <div class="products-pagination mt-4 mb-5 d-flex align-items-center justify-content-center"></div>
    </div>
  </section>
</main>
@endsection

@push('scripts')
<script>
$(document).ready(function(){
  // Set the maximum quantity from the database
  var maxQuantity = {{ $product->quantity }};
  
  // Function to update the Add to Cart button disabled state
  function updateSubmitButton($input) {
    var currentVal = parseInt($input.val());
    var $button = $input.closest('form').find('button[type=submit]');
    if(currentVal > maxQuantity){
      $button.prop('disabled', true);
    } else {
      $button.prop('disabled', false);
    }
  }
  
  // Increase button handler
  $('.qty-control__increase').on('click', function(){
    var $input = $(this).siblings('input.qty-control__number');
    var currentVal = parseInt($input.val());
    var $error = $(this).closest('.qty-control').siblings('.qty-error');
    if(currentVal < maxQuantity){
      $input.val(currentVal);
      $error.text(''); // Clear error
    } else {
      $error.text("Only " + maxQuantity + " items are available.");
    }
    updateSubmitButton($input);
  });
  
  // Decrease button handler
  $('.qty-control__reduce').on('click', function(){
    var $input = $(this).siblings('input.qty-control__number');
    var currentVal = parseInt($input.val());
    var $error = $(this).closest('.qty-control').siblings('.qty-error');
    if(currentVal > 1){
      $input.val(currentVal);
      $error.text(''); // Clear error if any
    }
    updateSubmitButton($input);
  });
  
  // Check manual changes to the input field
  $('input.qty-control__number').on('change', function(){
    var $input = $(this);
    var currentVal = parseInt($input.val());
    var $error = $input.closest('.qty-control').siblings('.qty-error');
    if(currentVal > maxQuantity){
      $error.text("Only " + maxQuantity + " items are available.");
      $input.val(maxQuantity);
    } else {
      $error.text('');
    }
    if(currentVal < 1 || isNaN(currentVal)){
      $input.val(1);
    }
    updateSubmitButton($input);
  });
});
</script>
@endpush
