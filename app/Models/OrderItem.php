<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }   
    
    public function review()
    {
        return $this->hasOne(Review::class,'order_item_id');
    }

    public function productVariation()
    {
        return $this->belongsTo(Product_Variations::class);
    }
}