<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function category(){
        return $this->belongsTo(Category::class,'category_id');
    }

    public function brand(){
        return $this->belongsTo(Brand::class,'brand_id');
    }

    public function categories() {
        return $this->belongsTo(Category::class);
    }

    public function brands() {
        return $this->belongsTo(Brand::class);
    }

    public function productVariations() {
        return $this->hasMany(Product_Variations::class);
    }
    public function sizes() {
        return $this->belongsToMany(Sizes::class, 'product_variations', 'product_id', 'size_id');
    }

    public function subcategory() {
        return $this->belongsTo(Subcategory::class);
    }
    
}
