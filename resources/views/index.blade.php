@extends("layouts.app")
@section("content")
<style>
.section-title {
    font-size: 1.8rem; /* Slightly larger font size */
    font-weight: 700; /* Stronger bold effect */
    text-transform: uppercase;
    letter-spacing: 1.5px; /* Increase letter spacing for a more refined look */
    color: #333;
    margin-bottom: 2rem; /* Increase space below the heading */
    font-family: 'Roboto', sans-serif; /* Modern font family */
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1); /* Soft shadow to make it stand out */
}

.section-title a {
    font-size: 1.1rem; /* Slightly larger font size for "View All" */
    font-weight: 500;
    color: #666;
    text-decoration: none;
    margin-left: 0.6rem; /* More space between title and "View All" */
    font-family: 'Lora', serif; /* Serif font for a sophisticated feel */
    transition: color 0.3s ease; /* Smooth transition effect */
}

.section-title a:hover {
    color: #000; /* Darker color on hover */
    text-decoration: underline; /* Add underline on hover */
}

.pc__img {
    width: 300px; /* Slightly larger image width */
    height: auto;
    border-radius: 8px; /* Rounded corners for images */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Soft shadow around images */
}

.swiper-slide {
    margin-bottom: 2rem; /* Increase space between slider items */
}

/* Add spacing between Hot Deals and Slider */
.hot-deals {
    margin-bottom: 3rem; /* Space below Hot Deals */
}

/* Add spacing between Featured Products and Hot Deals */
.featured-products {
    margin-top: 3rem; /* Space above Featured Products */
    margin-bottom: 3rem; /* Space below Featured Products */
}

/* Add spacing between New Arrivals and Featured Products */
.new-arrivals {
    margin-top: 3rem; /* Space above New Arrivals */
}

/* Sold Out Badge Style */


.sold-out-badge {
    position: absolute;
    bottom: 47px;
    right: 53px;
    background: #ff9800;
    color: #fff;
    font-size: 0.9rem;
    font-weight: bold;
    padding: 4px 8px;
    border-radius: 5px;
    z-index: 10;
  }

  /* .pc__sold-out {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(0,0,0,0.7);
    color: #fff;
    padding: 5px 10px;
    font-weight: bold;
    border-radius: 3px;
    z-index: 10;
  } */
</style>
<main>
  <!-- Slideshow Section -->
  <section class="swiper-container js-swiper-slider swiper-number-pagination slideshow" data-settings='{
    "autoplay": { "delay": 3000 },
    "slidesPerView": 1,
    "effect": "fade",
    "loop": true
  }'>
    <div class="swiper-wrapper">
      @foreach($slides as $slide)
        <div class="swiper-slide">
          <div class="overflow-hidden position-relative h-100">
            <div class="slideshow-character position-absolute bottom-0 pos_right-center">
              <img loading="lazy" src="{{ asset('uploads/slides') }}/{{$slide->image}}" width="542" height="733"
                alt="Slide Image"
                class="slideshow-character__img animate animate_fade animate_btt animate_delay-9 w-auto h-auto" />
              <div class="character_markup type2">
                <p class="text-uppercase font-sofia mark-grey-color animate animate_fade animate_btt animate_delay-10 mb-0">
                  {{$slide->tagline}}
                </p>
              </div>
            </div>
            <div class="slideshow-text container position-absolute start-50 top-50 translate-middle">
              <h2 class="h1 fw-normal mb-0 animate animate_fade animate_btt animate_delay-5">{{$slide->title}}</h2>
              <h2 class="h1 fw-bold animate animate_fade animate_btt animate_delay-5">{{$slide->subtitle}}</h2>
              <a href="{{$slide->link}}"
                class="btn-link btn-link_lg default-underline fw-medium animate animate_fade animate_btt animate_delay-7">Shop Now</a>
            </div>
          </div>
        </div>
      @endforeach
    </div>
    <div class="container">
      <div class="slideshow-pagination slideshow-number-pagination d-flex align-items-center position-absolute bottom-0 mb-5">
      </div>
    </div>
  </section>

  <!-- Hot Deals Section -->
  <section class="hot-deals container mt-5">
    <div class="text-center mb-4">
      <h2 class="section-title">Hot Deals <a href="{{ route('shop.index', ['filter' => 'hot-deals']) }}">(View All)</a></h2>
    </div>
    <div class="row">
      <div class="col-12">
        <div class="position-relative">
          <div class="swiper-container js-swiper-slider" data-settings='{
            "autoplay": { "delay": 1500 },
            "slidesPerView": 4,
            "slidesPerGroup": 4,
            "effect": "none",
            "loop": false,
            "breakpoints": {
              "320": { "slidesPerView": 2, "slidesPerGroup": 2, "spaceBetween": 14 },
              "768": { "slidesPerView": 2, "slidesPerGroup": 3, "spaceBetween": 24 },
              "992": { "slidesPerView": 3, "slidesPerGroup": 1, "spaceBetween": 30 },
              "1200": { "slidesPerView": 4, "slidesPerGroup": 1, "spaceBetween": 30 }
            }
          }'>
            <div class="swiper-wrapper">
              @foreach($sproducts as $sproduct)
                <div class="swiper-slide product-card product-card_style3" style="position: relative;">
                  <div class="pc__img-wrapper">
                    <a href="{{ route('shop.product.details', ['product_slug' => $sproduct->slug]) }}">
                      <img loading="lazy" src="{{ asset('uploads/products/' . $sproduct->image) }}" width="200" height="auto" alt="{{ $sproduct->name }}" class="pc__img">
                    </a>
                    @if($sproduct->quantity <= 0)
                      <div class="sold-out-badge">Sold Out</div>
                    @endif
                  </div>
                  <div class="pc__info position-relative">
                    <h6 class="pc__title">
                      <a href="{{ route('shop.product.details', ['product_slug' => $sproduct->slug]) }}">
                        {{ $sproduct->name }}
                      </a>
                    </h6>
                    <div class="product-card__price d-flex">
                      <span class="money price text-secondary">
                        @if($sproduct->sale_price)
                          <s>PKR {{ $sproduct->regular_price }}</s> PKR {{ $sproduct->sale_price }}
                        @else
                          PKR {{ $sproduct->regular_price }}
                        @endif
                      </span>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Featured Products Section -->
  <section class="featured-products container mt-5">
    <div class="text-center mb-4">
      <h2 class="section-title">Featured Products <a href="{{ route('shop.index', ['filter' => 'featured']) }}">(View All)</a></h2>
    </div>
    <div class="row">
      <div class="col-12">
        <div class="position-relative">
          <div class="swiper-container js-swiper-slider" data-settings='{
            "autoplay": { "delay": 1500 },
            "slidesPerView": 4,
            "slidesPerGroup": 4,
            "effect": "none",
            "loop": false,
            "breakpoints": {
              "320": { "slidesPerView": 2, "slidesPerGroup": 2, "spaceBetween": 14 },
              "768": { "slidesPerView": 2, "slidesPerGroup": 3, "spaceBetween": 24 },
              "992": { "slidesPerView": 3, "slidesPerGroup": 1, "spaceBetween": 30 },
              "1200": { "slidesPerView": 4, "slidesPerGroup": 1, "spaceBetween": 30 }
            }
          }'>
            <div class="swiper-wrapper">
              @foreach($fproducts as $fproduct)
                <div class="swiper-slide product-card product-card_style3" style="position: relative;">
                  <div class="pc__img-wrapper">
                    <a href="{{ route('shop.product.details', ['product_slug' => $fproduct->slug]) }}">
                      <img loading="lazy" src="{{ asset('uploads/products/' . $fproduct->image) }}" width="200" height="auto" alt="{{ $fproduct->name }}" class="pc__img">
                    </a>
                    @if($fproduct->quantity <= 0)
                      <div class="sold-out-badge">Sold Out</div>
                    @endif
                  </div>
                  <div class="pc__info position-relative">
                    <h6 class="pc__title">
                      <a href="{{ route('shop.product.details', ['product_slug' => $fproduct->slug]) }}">
                        {{ $fproduct->name }}
                      </a>
                    </h6>
                    <div class="product-card__price d-flex">
                      <span class="money price text-secondary">
                        @if($fproduct->sale_price)
                          <s>PKR {{ $fproduct->regular_price }}</s> PKR {{ $fproduct->sale_price }}
                        @else
                          PKR {{ $fproduct->regular_price }}
                        @endif
                      </span>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- New Arrivals Section -->
  <section class="new-arrivals container mt-5">
    <div class="text-center mb-4">
      <h2 class="section-title">New Arrivals <a href="{{ route('shop.index', ['filter' => 'new-arrivals']) }}">(View All)</a></h2>
    </div>
    <div class="row">
      <div class="col-12">
        <div class="position-relative">
          <div class="swiper-container js-swiper-slider" data-settings='{
            "autoplay": { "delay": 1500 },
            "slidesPerView": 4,
            "slidesPerGroup": 4,
            "effect": "none",
            "loop": false,
            "breakpoints": {
              "320": { "slidesPerView": 2, "slidesPerGroup": 2, "spaceBetween": 14 },
              "768": { "slidesPerView": 2, "slidesPerGroup": 3, "spaceBetween": 24 },
              "992": { "slidesPerView": 3, "slidesPerGroup": 1, "spaceBetween": 30 },
              "1200": { "slidesPerView": 4, "slidesPerGroup": 1, "spaceBetween": 30 }
            }
          }'>
            <div class="swiper-wrapper">
              @foreach($newArrivals as $product)
                <div class="swiper-slide product-card product-card_style3" style="position: relative;">
                  <div class="pc__img-wrapper">
                    <a href="{{ route('shop.product.details', ['product_slug' => $product->slug]) }}">
                      <img loading="lazy" src="{{ asset('uploads/products/' . $product->image) }}" width="200" height="auto" alt="{{ $product->name }}" class="pc__img">
                    </a>
                    @if($product->quantity <= 0)
                      <div class="sold-out-badge">Sold Out</div>
                    @endif
                  </div>
                  <div class="pc__info position-relative">
                    <h6 class="pc__title">
                      <a href="{{ route('shop.product.details', ['product_slug' => $product->slug]) }}">
                        {{ $product->name }}
                      </a>
                    </h6>
                    <div class="product-card__price d-flex">
                      <span class="money price text-secondary">
                        @if($product->sale_price)
                          <s>PKR {{ $product->regular_price }}</s> PKR {{ $product->sale_price }}
                        @else
                          PKR {{ $product->regular_price }}
                        @endif
                      </span>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>
@endsection
