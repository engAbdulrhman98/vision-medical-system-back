<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class ProductsImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $nameAr = $row['name_arabic'] ?? $row['name_ar'] ?? null;
        $nameEn = $row['name_english'] ?? $row['name_en'] ?? null;
        $categoryId = $row['category_id'] ?? null;
        $brandId = $row['brand_id'] ?? null;
        $price = $row['price'] ?? 0;
        $sku = $row['sku'] ?? null;
        $descAr = $row['description_arabic'] ?? $row['description_ar'] ?? '';
        $descEn = $row['description_english'] ?? $row['description_en'] ?? '';
        $detailsAr = $row['details_arabic'] ?? $row['details_ar'] ?? '';
        $detailsEn = $row['details_english'] ?? $row['details_en'] ?? '';
        $image = $row['image_url'] ?? $row['image'] ?? 'https://images.unsplash.com/photo-1516549655169-df83a0774514?auto=format&fit=crop&w=800&q=80';
        $inStock = isset($row['in_stock']) ? (bool) $row['in_stock'] : true;

        if (empty($nameAr) || empty($nameEn) || empty($categoryId) || empty($brandId)) {
            return null;
        }

        $slug = Str::slug($nameEn);
        if (empty($slug) || $slug == '-') {
            $slug = str_replace(' ', '-', $nameEn);
        }

        return new Product([
            'name' => [
                'ar' => $nameAr,
                'en' => $nameEn,
            ],
            'category_id' => $categoryId,
            'brand_id' => $brandId,
            'price' => $price,
            'sku' => $sku,
            'description' => [
                'ar' => $descAr,
                'en' => $descEn,
            ],
            'details' => [
                'ar' => $detailsAr,
                'en' => $detailsEn,
            ],
            'image' => $image,
            'in_stock' => $inStock,
            'slug' => $slug,
        ]);
    }
}
