<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CategoriesExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Category::all();
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
            'Description (Arabic)',
            'Description (English)',
            'Image URL',
            'Created At',
        ];
    }

    /**
     * @param mixed $category
     * @return array
     */
    public function map($category): array
    {
        return [
            $category->id,
            $category->getTranslation('name', 'ar'),
            $category->getTranslation('name', 'en'),
            $category->getTranslation('description', 'ar'),
            $category->getTranslation('description', 'en'),
            $category->image,
            $category->created_at ? $category->created_at->toDateTimeString() : '',
        ];
    }
}
