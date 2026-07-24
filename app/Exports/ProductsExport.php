<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Product::with(['category', 'brand'])->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name (Arabic)',
            'Name (English)',
            'Category ID',
            'Brand ID',
            'Price',
            'SKU',
            'Description (Arabic)',
            'Description (English)',
            'Details (Arabic)',
            'Details (English)',
            'Image URL',
            'In Stock',
            'Created At',
        ];
    }

    /**
     * @param mixed $product
     * @return array
     */
    public function map($product): array
    {
        return [
            $product->id,
            $product->getTranslation('name', 'ar'),
            $product->getTranslation('name', 'en'),
            $product->category_id,
            $product->brand_id,
            $product->price,
            $product->sku,
            $product->getTranslation('description', 'ar') ?? '',
            $product->getTranslation('description', 'en') ?? '',
            $product->getTranslation('details', 'ar') ?? '',
            $product->getTranslation('details', 'en') ?? '',
            $product->image,
            $product->in_stock ? 1 : 0,
            $product->created_at ? $product->created_at->toDateTimeString() : '',
        ];
    }
}
