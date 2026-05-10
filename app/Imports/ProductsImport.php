<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
// use Illuminate\Support\Facades\Validator;

class ProductsImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use SkipsErrors, SkipsFailures;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $sku = trim(strtoupper($row['sku'] ?? ''));

            if ($sku === '') {
                $sku = $this->generateUniqueSku();
            }

            $categoryName = isset($row['category']) ? trim($row['category']) : null;
            $categoryId = $this->getCategoryId($categoryName);

            Product::updateOrCreate(
                ['sku' => $sku],
                [
                    'name' => $row['name'] ?? 'Unknown Product',
                    'description' => $row['description'] ?? null,
                    'price' => $row['price'] ?? 0,
                    'category_id' => $categoryId,
                ]
            );

            // Handle images if provided in import
            // if (isset($row['images']) && !empty($row['images'])) {
            //     $this->processImages($product, $row['images']);
            // }
        }
    }

    public function rules(): array
    {
        return [
            '*.name' => 'required|string|max:255',
            '*.price' => 'nullable|numeric|min:0',
            '*.category' => 'nullable|string|max:255',
            '*.sku' => 'required|string|max:100',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            '*.name.required' => 'Product name is required',
            // '*.sku.required' => 'SKU is required',
            '*.price.numeric' => 'Price must be a number',
        ];
    }

    private function generateUniqueSku(): string
    {
        do {
            $sku = 'PROD-' . Str::random(8) . '-' . time();
        } while (Product::where('sku', $sku)->exists());

        return $sku;
    }

    private function getCategoryId($categoryName)
    {
        if (!$categoryName) {
            return null;
        }

        $category = Category::where('name', $categoryName)->first();

        if (!$category) {
            $category = Category::create([
                'name' => $categoryName
            ]);
        }

        return $category->id;
    }
}