<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product_Variations extends Model
{
    protected $table = 'product_variations';
    protected $fillable = ['product_id', 'size_id', 'color_id', 'price', 'quantity'];

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function size() {
        return $this->belongsTo(Sizes::class);
    }
    
}


