<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Size;
use App\Models\Color;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;
use App\Exports\ProductsExport;

class ProductController extends Controller
{
    // Show create form
    public function create()
    {
        $categories = Category::all();
        $sizes = Size::all();
        $colors = Color::all();
        return view('admin.products.create', compact('categories', 'sizes', 'colors'));
    }

    // Store new product
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:200',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'image'       => $imagePath,
            'category_id' => $request->category_id,
        ]);

        if ($request->sizes) {
            $product->sizes()->sync($request->sizes);
        }

        if ($request->colors) {
            $product->colors()->sync($request->colors);
        }

        return redirect()->route('admin.dashboard')->with('success', 'Product created!');
    }

    // Show edit form
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        $sizes = Size::all();
        $colors = Color::all();

        return view('admin.products.edit', compact('product', 'categories', 'sizes', 'colors'));
    }

    // Update product
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'  => 'required|string|max:200',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $product = Product::findOrFail($id);

        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product->update([
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'image'       => $imagePath,
            'category_id' => $request->category_id,
        ]);

        $product->sizes()->sync($request->sizes ?? []);
        $product->colors()->sync($request->colors ?? []);

        return redirect()->route('admin.dashboard')->with('success', 'Product updated!');
    }

    // Delete product
    public function destroy($id)
    {
        Product::findOrFail($id)->delete();
        return redirect()->route('admin.dashboard')->with('success', 'Product deleted!');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file'
        ]);

        try {
            Excel::import(new ProductsImport, $request->file('file'));

            $message = "Products imported successfully";

            return redirect()->route('admin.dashboard')
                    ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $type = $request->type;

        $fileName = 'products_'.now()->format('d-m-Y_H-i');

        if ($type == 'csv') {
            return Excel::download(new ProductsExport, 'products_'.now()->format('d-m-Y_H-i').'.csv');
        }

        return Excel::download(new ProductsExport, 'products_'.now()->format('d-m-Y_H-i').'.xlsx');
    }
}
