<?php

namespace App\Exports;

use App\Models\Brand;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BrandsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Brand::all();
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
            'Slug',
            'Created At',
        ];
    }

    /**
     * @param mixed $brand
     * @return array
     */
    public function map($brand): array
    {
        return [
            $brand->id,
            $brand->getTranslation('name', 'ar'),
            $brand->getTranslation('name', 'en'),
            $brand->slug,
            $brand->created_at->toDateTimeString(),
        ];
    }
}
