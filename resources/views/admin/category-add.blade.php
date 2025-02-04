@extends('layouts.admin')
@section('content')

<div class="main-content-inner">
    <!-- main-content-wrap -->
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Category Information</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{route('admin.index')}}"><div class="text-tiny">Dashboard</div></a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <a href="{{route('admin.categories')}}"><div class="text-tiny">Categories</div></a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">New Categories</div>
                </li>
            </ul>
        </div>
        <!-- new-category -->
        <div class="wg-box">
            <form class="form-new-product form-style-1" action="{{route('admin.category.store')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <fieldset class="name">
                    <div class="body-title">Category Name <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Category name" name="name" tabindex="0" value="{{old('name')}}" aria-required="true">
                </fieldset>
                @error("name") <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                <fieldset class="name">
                    <div class="body-title">Category Slug <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Category Slug" name="slug" tabindex="0" value="{{old('slug')}}" aria-required="true">
                </fieldset>
                @error("slug") <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                <fieldset>
                    <div class="body-title">Category Image <span class="tf-color-1">*</span></div>
                    <div class="upload-image flex-grow">
                        <div class="item" id="imgpreview" style="display:none">
                            <img src="{{asset('images/upload/upload-1.png')}}" class="effect8" alt="">
                        </div>
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
                @error("image") <span class="alert alert-danger text-center">{{$message}}</span> @enderror

                <!-- Subcategories Section -->
                <fieldset class="subcategories">
                    <div class="body-title">Subcategory Names <span class="tf-color-1">*</span></div>
                    <div id="subcategory-fields">
                        <div class="subcategory-field">
                            <input class="flex-grow" type="text" placeholder="Subcategory Name" name="subcategories[0][name]">
                        </div>
                    </div>
                    <button type="button" id="add-subcategory-btn" class="tf-button w208">Add Another Subcategory</button>
                </fieldset>

                <div class="bot">
                    <div></div>
                    <button class="tf-button w208" type="submit">Save</button>
                </div>
            </form>
        </div>
        <!-- /new-category -->
    </div>
    <!-- /main-content-wrap -->
</div>

@endsection

@push("scripts")
    <script>
        $(function(){
            $("#myFile").on("change",function(e){
                const photoInp = $("#myFile");                    
                const [file] = this.files;
                if (file) {
                    $("#imgpreview img").attr('src',URL.createObjectURL(file));
                    $("#imgpreview").show();                        
                }
            });

            $("input[name='name']").on("change",function(){
                $("input[name='slug']").val(StringToSlug($(this).val()));
            });

            // Add Subcategory Input Fields Dynamically
            let subcategoryCount = 1;
            function addSubcategoryField() {
                subcategoryCount++;
                const subcategoryFields = document.getElementById('subcategory-fields');
                const newField = `
                    <div class="subcategory-field">
                        <input class="flex-grow" type="text" placeholder="Subcategory Name" name="subcategories[${subcategoryCount - 1}][name]" required>
                    </div>
                `;
                subcategoryFields.insertAdjacentHTML('beforeend', newField);
            }

            $("#add-subcategory-btn").click(function() {
                addSubcategoryField();
            });
        });

        function StringToSlug(Text) {
            return Text.toLowerCase()
            .replace(/[^\w ]+/g, "")
            .replace(/ +/g, "-");
        }      
    </script>
@endpush
