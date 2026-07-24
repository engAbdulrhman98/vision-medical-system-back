<?php

namespace App\Exports;

use App\Models\Area;
use Maatwebsite\Excel\Concerns\FromCollection;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AreasExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Load parent relationship to avoid N+1 query issue
        return Area::with('parent')->get();
    }

    /**
    * @param Area $area
    * @return array
    */
    public function map($area): array
    {
        return [
            $area->id,
            $area->getTranslation('name', 'ar') ?: '',
            $area->getTranslation('name', 'en') ?: '',
            $area->type,
            $area->parent ? ($area->parent->getTranslation('name', 'ar') ?: $area->parent->getTranslation('name', 'en')) : '-',
            $area->created_at ? $area->created_at->toDateTimeString() : '-',
        ];
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        return [
            'ID',
            'الاسم بالعربية (Arabic Name)',
            'الاسم بالإنجليزية (English Name)',
            'النوع (Type)',
            'المنطقة الأب (Parent Area)',
            'تاريخ الإنشاء (Created At)',
        ];
    }
}
