@extends('layouts.admin')

@section('content')


    <!-- main-content-wrap -->
    <div class="main-content-inner">
        <!-- main-content-wrap -->
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Edit Product</h3>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                    <li>
                        <a href="{{route('admin.index')}}"><div class="text-tiny">Dashboard</div></a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <a href="{{route('admin.products')}}"><div class="text-tiny">Products</div></a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <div class="text-tiny">Edit product</div>
                    </li>
                </ul>
            </div>
            <!-- form-add-product -->
            <form class="tf-section-2 form-add-product" method="POST" enctype="multipart/form-data" action="{{route('admin.product.update')}}">
                <input type="hidden" name="id" value="{{$product->id}}" />
                @csrf
                @method("PUT")
                <div class="wg-box">
                    <fieldset class="name">
                        <div class="body-title mb-10">Product name <span class="tf-color-1">*</span></div>
                        <input class="mb-10" type="text" placeholder="Enter product name" name="name" tabindex="0" value="{{$product->name}}" aria-required="true" required="">
                        <div class="text-tiny">Do not exceed 100 characters when entering the product name.</div>
                    </fieldset>

                    <fieldset class="name">
                        <div class="body-title mb-10">Slug <span class="tf-color-1">*</span></div>
                        <input class="mb-10" type="text" placeholder="Enter product slug" name="slug" tabindex="0" value="{{$product->slug}}" aria-required="true" required="">
                        <div class="text-tiny">Do not exceed 100 characters when entering the product name.</div>
                    </fieldset>

                    <div class="gap22 cols">
                        <fieldset class="category">
                            <div class="body-title mb-10">Category <span class="tf-color-1">*</span></div>
                            <div class="select">
                                <select name="category_id" id="category_id">
                                    <option>Choose category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{$category->id}}" {{$product->category_id == $category->id ? "selected":""}}>{{$category->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </fieldset>

                        <fieldset class="subcategory">
                            <div class="body-title mb-10">Subcategory <span class="tf-color-1">*</span></div>
                            <div class="select">
                                <select name="subcategory_id" id="subcategory_id">
                                    <option>Choose subcategory</option>
                                    @foreach ($product->category->subcategories as $subcategory)
                                        <option value="{{$subcategory->id}}" {{$product->subcategory_id == $subcategory->id ? "selected":""}}>{{$subcategory->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </fieldset>
                    </div>

                    <fieldset class="brand">
                            <div class="body-title mb-10">Brand <span class="tf-color-1">*</span></div>
                            <div class="select">
                                <select name="brand_id">
                                    <option>Choose Brand</option>
                                    @foreach ($brands as $brand)
                                    <option value="{{$brand->id}}" {{$product->brand_id == $brand->id ? "selected":""}}>{{$brand->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </fieldset>

                    <fieldset class="shortdescription">
                        <div class="body-title mb-10">Short Description <span class="tf-color-1">*</span></div>
                        <textarea class="mb-10 ht-150" name="short_description" placeholder="Short Description" tabindex="0" aria-required="true" required="">{{$product->short_description}}</textarea>
                        <div class="text-tiny">Do not exceed 100 characters when entering the product name.</div>
                    </fieldset>
                    
                    <fieldset class="description">
                        <div class="body-title mb-10">Description <span class="tf-color-1">*</span></div>
                        <textarea class="mb-10" name="description" placeholder="Description" tabindex="0" aria-required="true" required="">{{$product->description}}</textarea>
                        <div class="text-tiny">Do not exceed 100 characters when entering the product name.</div>
                    </fieldset>
                </div>

                <div class="wg-box">
                    <fieldset>
                        <div class="body-title">Upload images <span class="tf-color-1">*</span></div>
                        <div class="upload-image flex-grow">
                            @if($product->image)
                                <div class="item" id="imgpreview">
                                    <img src="{{asset('uploads/products')}}/{{$product->image}}" class="effect8" alt="">
                                </div>
                            @endif
                            <div id="upload-file" class="item up-load">
                                <label class="uploadfile" for="myFile">
                                    <span class="icon">
                                        <i class="icon-upload-cloud"></i>
                                    </span>
                                    <span class="body-text">Drop your images here or select <span class="tf-color">click to browse</span></span>
                                    <input type="file" id="myFile" name="image" accept="image/*">
                                </label>
                            </div>
                        </div>
                    </fieldset> 

                    <fieldset>
                        <div class="body-title mb-10">Upload Gallery Images</div>
                        <div class="upload-image mb-16">
                            @if($product->images)
                                @foreach(explode(",", $product->images) as $img)
                                    <div class="item gitems">
                                        <img src="{{asset('uploads/products')}}/{{($img)}}" class="effect8" alt="">
                                    </div>
                                @endforeach
                            @endif

                            <div id="galUpload" class="item up-load">
                                <label class="uploadfile" for="gFile">
                                    <span class="icon">
                                        <i class="icon-upload-cloud"></i>
                                    </span>
                                    <span class="text-tiny">Drop your images here or select <span class="tf-color">click to browse</span></span>
                                    <input type="file" id="gFile" name="images[]" accept="image/*" multiple>
                                </label>
                            </div>
                        </div>
                    </fieldset>

                    <div class="cols gap22">
                        <fieldset class="name">
                            <div class="body-title mb-10">Regular Price <span class="tf-color-1">*</span></div>
                            <input class="mb-10" type="text" placeholder="Enter regular price" name="regular_price" tabindex="0" value="{{$product->regular_price}}" aria-required="true" required=""/>
                        </fieldset>
                        <fieldset class="name">
                            <div class="body-title mb-10">Sale Price <span class="tf-color-1">*</span></div>
                            <input class="mb-10" type="text" placeholder="Enter sale price" name="sale_price" tabindex="0" value="{{$product->sale_price}}" aria-required="true"/>
                        </fieldset>
                    </div>

                    <div class="cols gap22">
                        <fieldset class="name">
                            <div class="body-title mb-10">SKU <span class="tf-color-1">*</span></div>
                            <input class="mb-10" type="text" placeholder="Enter SKU" name="SKU" tabindex="0" value="{{$product->SKU}}" aria-required="true" required=""/>
                        </fieldset>
                        <fieldset class="name">
                            <div class="body-title mb-10">Quantity <span class="tf-color-1">*</span></div>
                            <input class="mb-10" type="text" placeholder="Enter quantity" name="quantity" tabindex="0" value="{{$product->quantity}}" aria-required="true" required=""/>
                        </fieldset>
                    </div>

                    <div class="cols gap22">
                        <fieldset class="name">
                            <div class="body-title mb-10">Stock</div>
                            <div class="select mb-10">
                                <select name="stock_status">
                                    <option value="instock" {{$product->stock_status == "instock" ? "selected" : ""}}>InStock</option>
                                    <option value="outofstock" {{$product->stock_status == "outofstock" ? "selected" : ""}}>Out of Stock</option>
                                </select>
                            </div>
                        </fieldset>
                        <fieldset class="name">
                            <div class="body-title mb-10">Featured</div>
                            <div class="select mb-10">
                                <select name="featured">
                                    <option value="0" {{$product->featured == "0" ? "selected" : ""}}>No</option>
                                    <option value="1" {{$product->featured == "1" ? "selected" : ""}}>Yes</option>
                                </select>
                            </div>
                        </fieldset>
                    </div>

                    <fieldset class="sizes">
                        <div class="body-title mb-10">Sizes <span class="tf-color-1">*</span></div>
                        <div class="checkbox-group">
                            @foreach ($sizes as $size)
                                <label>
                                    <input type="checkbox" name="sizes[]" value="{{ $size->id }}" 
                                    @if(in_array($size->id, old('sizes', $product->sizes->pluck('id')->toArray()))) checked @endif>
                                    {{ $size->name }}
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                    @error("sizes") <span class="alert alert-danger text-center">{{$message}}</span> @enderror

                    <div class="cols gap10">
                        <button class="tf-button w-full" type="submit">Save product</button>
                    </div>
                </div>
            </form>
            <!-- /form-add-product -->
        </div>
        <!-- /main-content-wrap -->
    </div>
    <!-- /main-content-wrap -->
@endsection

@push("scripts")
<script>
    $(function(){
        // Update subcategory options based on category
        $("#category_id").on("change", function() {
            var category_id = $(this).val();
            $.ajax({
                url: "/admin/getSubcategories/" + category_id, // Define this route
                method: "GET",
                success: function(response) {
                    var subcategorySelect = $("#subcategory_id");
                    subcategorySelect.empty(); // Clear previous options
                    subcategorySelect.append('<option>Choose subcategory</option>');
                    response.subcategories.forEach(function(subcategory) {
                        subcategorySelect.append('<option value="' + subcategory.id + '">' + subcategory.name + '</option>');
                    });
                }
            });
        });

        // Handle image uploads
        $("#myFile").on("change",function(e){
            const photoInp = $("#myFile");
            const [file] = this.files;
            if (file) {
                $("#imgpreview img").attr('src',URL.createObjectURL(file));
                $("#imgpreview").show();
            }
        });

        $("#gFile").on("change",function(e){
            $(".gitems").remove();
            const gphotos = this.files;
            $.each(gphotos,function(key,val){
                $("#galUpload").prepend(`<div class="item gitems"><img src="${URL.createObjectURL(val)}" alt=""></div>`);
            });
        });

        // Auto generate slug
        $("input[name='name']").on("change",function(){
            $("input[name='slug']").val(StringToSlug($(this).val()));
        });
    });

    function StringToSlug(Text) {
        return Text.toLowerCase()
        .replace(/[^\w ]+/g, "")
        .replace(/ +/g, "-");
    }
</script>
@endpush
