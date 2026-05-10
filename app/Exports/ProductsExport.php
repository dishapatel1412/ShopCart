<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsExport implements FromCollection, WithMapping, WithHeadings
{
    public function collection()
    {
        return Product::with('category')->get();
    }

    public function map($product): array
    {
        return [
            $product->name,
            $product->sku,
            $product->price,
            $product->description,
            $product->category->name ?? null,
        ];
    }

    public function headings(): array
    {
        return ['Name', 'SKU', 'Price', 'Description', 'Category'];
    }
}
