<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::where('is_active', '1')->get();
        return view('products.index', compact('products'));
    }

    public function show($id)
    {
        $product = Product::where('is_active', '1')
            ->with(['category', 'sizes', 'colors'])
            ->findOrFail($id);
            
        return view('products.show', compact('product'));
    }
}
