<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;
use App\Exports\ProductsExport;
use App\Models\Product; 

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('is_active', '1');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where('name', 'LIKE', "%{$search}%");
        }

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->category}%");
            });
        }
        $products = $query->paginate(10);

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'is_active' => '1'
        ]);
        return response()->json($product, 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price
        ]);
        return response()->json($product);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['is_active' => '0']);
        return response()->json(['message' => 'Product deactivated']);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file'
        ]);

        try{
            Excel::import(new ProductsImport, $request->file('file'));
            return response()->json(['message' => 'Products imported successfully']);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong. Please try again']);
        }
    }

    public function export()
    {
        return Excel::download(new ProductsExport, 'products_'.now()->format('d-m-Y_H-i').'.csv');

        // $products = Excel::download(new ProductsExport, 'product.csv');
        // return response()->json($products);
    }
}
