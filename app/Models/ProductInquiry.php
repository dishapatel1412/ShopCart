<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductInquiry extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'subject',
        'message',
        'reply_message',
        'replied_at',
        'replied_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    public function admin()
    {
        return $this->belongsTo(User::class, 'replied_by');
    }
}
