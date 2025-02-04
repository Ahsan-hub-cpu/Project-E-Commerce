<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'email', 'subtotal', 'discount', 'tax', 'total',
        'name', 'phone', 'locality', 'address', 'city', 'state', 'country',
        'landmark', 'zip', 'type', 'status', 'is_shipping_different',
        'delivered_date', 'canceled_date'
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }
}