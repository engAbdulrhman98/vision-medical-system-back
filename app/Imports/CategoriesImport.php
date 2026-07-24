<?php

namespace App\Imports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class CategoriesImport implements ToModel, WithHeadingRow
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
        $descAr = $row['description_arabic'] ?? $row['description_ar'] ?? '';
        $descEn = $row['description_english'] ?? $row['description_en'] ?? '';
        $image = $row['image_url'] ?? $row['image'] ?? 'https://images.unsplash.com/photo-1516549655169-df83a0774514?auto=format&fit=crop&w=800&q=80';

        if (empty($nameAr) || empty($nameEn)) {
            return null;
        }

        $slug = Str::slug($nameEn);
        if (empty($slug) || $slug == '-') {
            $slug = str_replace(' ', '-', $nameEn);
        }

        return new Category([
            'name' => [
                'ar' => $nameAr,
                'en' => $nameEn,
            ],
            'description' => [
                'ar' => $descAr,
                'en' => $descEn,
            ],
            'slug' => $slug,
            'image' => $image,
        ]);
    }
}
