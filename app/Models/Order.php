<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'subtotal',
        'discount',
        'tax',
        'total',
        'coupon_code',
        'name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'pincode',
        'status',
        'payment_method',
        'payment_status',
        'payment_id',
        'cashfree_order_id',
    ];

    public static $statuses = [
        'pending',
        'confirmed',
        'shipped',
        'delivered'
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
