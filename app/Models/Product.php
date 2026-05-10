<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Size;
use App\Models\Color;

class Product extends Model
{
    protected $fillable = ['name', 'sku', 'description', 'image', 'price', 'category_id', 'is_active'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function sizes() {
        return $this->belongsToMany(Size::class, 'product_sizes');
    }

    public function colors() {
        return $this->belongsToMany(Color::class, 'product_colors');
    }
}
