<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sizes extends Model
{
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_variations', 'size_id', 'product_id');
    }

    public function subCategory()
{
    return $this->belongsTo(SubCategory::class);
}
}