<?php

namespace App\Imports;

use App\Models\Brand;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BrandsImport implements ToModel, WithHeadingRow
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

        if (empty($nameAr) || empty($nameEn)) {
            return null;
        }

        return new Brand([
            'name' => [
                'ar' => $nameAr,
                'en' => $nameEn,
            ],
        ]);
    }
}
