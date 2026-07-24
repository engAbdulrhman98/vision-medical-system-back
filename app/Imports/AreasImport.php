<?php

namespace App\Imports;

use App\Models\Area;
use Maatwebsite\Excel\Concerns\ToModel;

use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class AreasImport implements ToModel, WithStartRow, SkipsEmptyRows
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // $row[0] is ID (we ignore/generate new or update)
        // $row[1] is Arabic Name
        // $row[2] is English Name
        // $row[3] is Type (governorate, city, etc.)
        // $row[4] is Parent Area Name
        
        $arabicName = isset($row[1]) ? trim($row[1]) : null;
        $englishName = isset($row[2]) ? trim($row[2]) : null;
        $type = isset($row[3]) ? strtolower(trim($row[3])) : 'city';
        $parentNameInput = isset($row[4]) ? trim($row[4]) : null;

        if (empty($arabicName) && empty($englishName)) {
            return null;
        }

        // Resolve parent_id by name if provided
        $parentId = null;
        if (!empty($parentNameInput) && $parentNameInput !== '-') {
            $parent = Area::where('name->ar', $parentNameInput)
                ->orWhere('name->en', $parentNameInput)
                ->first();
            if ($parent) {
                $parentId = $parent->id;
            }
        }

        // Avoid duplication by updating or creating based on translation names
        return Area::updateOrCreate(
            [
                'name->ar' => $arabicName,
                'name->en' => $englishName,
            ],
            [
                'name' => [
                    'ar' => $arabicName,
                    'en' => $englishName,
                ],
                'type' => $type,
                'parent_id' => $parentId,
            ]
        );
    }

    /**
    * @return int
    */
    public function startRow(): int
    {
        return 2; // Skip the headers row
    }
}
