<?php

namespace App\Imports;

use App\Models\Client;
use App\Models\ClientContact;
use App\Models\Area;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ClientsImportRegistry
{
    /** @var array<int|string, int> Maps excel_client_id or client_name to db_client_id */
    public static array $clientMap = [];
}

class ClientsImport implements WithMultipleSheets
{
    /**
     * @return array
     */
    public function sheets(): array
    {
        return [
            0 => new ClientsSheetImport(),
            1 => new ClientContactsSheetImport(),
        ];
    }
}

class ClientsSheetImport implements ToModel, WithStartRow, SkipsEmptyRows
{
    /**
     * @param array $row
     * @return Model|array|null
     */
    public function model(array $row): Model|array|null
    {
        // Headers:
        // row[0]: كود العميل (ID)
        // row[1]: العميل (Client Name)
        // row[2]: نوع العميل (Type)
        // row[3]: المحافظه (Governorate)
        // row[4]: المدينه (City)
        // row[5]: المنطقه/المركز (Region / Area)
        // row[6]: العنوان بالتفصيل (Detailed Address)
        // row[7]: رقم الهاتف (Phone)

        $excelId = isset($row[0]) && is_numeric(trim($row[0])) ? (int) trim($row[0]) : null;
        $clientName = isset($row[1]) ? trim($row[1]) : null;
        $type = isset($row[2]) && !empty(trim($row[2])) ? trim($row[2]) : 'غير محدد';
        $governorate = isset($row[3]) && !empty(trim($row[3])) ? trim($row[3]) : 'غير معروف';
        $city = isset($row[4]) && !empty(trim($row[4])) ? trim($row[4]) : 'غير معروف';
        $areaNameInput = isset($row[5]) ? trim($row[5]) : null;
        $detailedAddress = isset($row[6]) && !empty(trim($row[6])) ? trim($row[6]) : 'غير معروف';
        $phone = isset($row[7]) && !empty(trim($row[7])) ? trim($row[7]) : 'غير معروف';

        if (empty($clientName)) {
            return null;
        }

        // 1. Resolve area_id by searching for the Area with Arabic normalization
        $areaId = $this->findMatchingArea($areaNameInput);
        if (!$areaId) {
            $areaId = $this->findMatchingArea($city);
        }
        if (!$areaId) {
            $areaId = $this->findMatchingArea($governorate);
        }

        if (!$areaId) {
            $fallback = Area::first();
            $areaId = $fallback ? $fallback->id : 1;
        }

        // 2. Find existing client by excelId or Name
        $client = null;
        if ($excelId) {
            $client = Client::find($excelId);
        }

        if (!$client) {
            $client = Client::where('name->ar', $clientName)
                ->orWhere('name->en', $clientName)
                ->orWhere('name', $clientName)
                ->first();
        }

        $attributes = [
            'name' => [
                'ar' => $clientName,
                'en' => $clientName,
            ],
            'type' => $type,
            'area_id' => $areaId,
            'governorate' => $governorate,
            'city' => $city,
            'detailed_address' => $detailedAddress,
            'address' => [
                'ar' => $detailedAddress ?: '',
                'en' => $detailedAddress ?: '',
            ],
            'phone' => $phone,
        ];

        if ($client) {
            $client->update($attributes);
        } else {
            $client = Client::create($attributes);
        }

        // Register client mapping
        if ($excelId) {
            ClientsImportRegistry::$clientMap[$excelId] = $client->id;
        }
        ClientsImportRegistry::$clientMap[$clientName] = $client->id;

        return $client;
    }

    private function normalizeArabic(string $text): string
    {
        $text = mb_strtolower(trim($text));
        $text = str_replace(['أ', 'إ', 'آ'], 'ا', $text);
        $text = str_replace('ة', 'ه', $text);
        $text = str_replace('ى', 'ي', $text);
        return $text;
    }

    private function findMatchingArea(?string $input): ?int
    {
        if (empty($input) || $input === '-') {
            return null;
        }

        $area = Area::where('name->ar', $input)
            ->orWhere('name->en', $input)
            ->orWhere('name', 'like', "%{$input}%")
            ->first();

        if ($area) {
            return $area->id;
        }

        $normInput = $this->normalizeArabic($input);
        if (!empty($normInput)) {
            $allAreas = Area::all();
            foreach ($allAreas as $a) {
                $nameAr = $a->getTranslation('name', 'ar') ?: (is_string($a->name) ? $a->name : '');
                $nameEn = $a->getTranslation('name', 'en') ?: '';
                
                if (!empty($nameAr) && (str_contains($this->normalizeArabic($nameAr), $normInput) || str_contains($normInput, $this->normalizeArabic($nameAr)))) {
                    return $a->id;
                }
                if (!empty($nameEn) && (stripos($nameEn, $input) !== false || stripos($input, $nameEn) !== false)) {
                    return $a->id;
                }
            }
        }

        return null;
    }

    public function startRow(): int
    {
        return 2;
    }
}

class ClientContactsSheetImport implements ToModel, WithStartRow, SkipsEmptyRows
{
    /**
     * @param array $row
     * @return Model|array|null
     */
    public function model(array $row): Model|array|null
    {
        // Headers:
        // row[0]: كود العميل
        // row[1]: اسم العميل
        // row[2]: اسم المسئول
        // row[3]: الوظيفة / الصفة
        // row[4]: رقم الهاتف

        $clientRefId = isset($row[0]) ? trim($row[0]) : null;
        $clientRefName = isset($row[1]) ? trim($row[1]) : null;
        $contactName = isset($row[2]) ? trim($row[2]) : null;
        $jobTitle = isset($row[3]) ? trim($row[3]) : null;
        $phone = isset($row[4]) ? trim($row[4]) : null;

        if (empty($contactName)) {
            return null;
        }

        $clientId = null;

        // 1. Try registry by excel ID
        if ($clientRefId && is_numeric($clientRefId) && isset(ClientsImportRegistry::$clientMap[(int)$clientRefId])) {
            $clientId = ClientsImportRegistry::$clientMap[(int)$clientRefId];
        }

        // 2. Try DB by ID
        if (!$clientId && $clientRefId && is_numeric($clientRefId)) {
            $c = Client::find((int)$clientRefId);
            if ($c) {
                $clientId = $c->id;
            }
        }

        // 3. Try registry by name
        if (!$clientId && $clientRefName && isset(ClientsImportRegistry::$clientMap[$clientRefName])) {
            $clientId = ClientsImportRegistry::$clientMap[$clientRefName];
        }

        // 4. Try registry by refId if text
        if (!$clientId && $clientRefId && isset(ClientsImportRegistry::$clientMap[$clientRefId])) {
            $clientId = ClientsImportRegistry::$clientMap[$clientRefId];
        }

        // 5. Try DB by name
        if (!$clientId && $clientRefName) {
            $c = Client::where('name->ar', $clientRefName)
                ->orWhere('name->en', $clientRefName)
                ->orWhere('name', $clientRefName)
                ->first();
            if ($c) {
                $clientId = $c->id;
            }
        }

        if (!$clientId) {
            return null;
        }

        return ClientContact::updateOrCreate(
            [
                'client_id' => $clientId,
                'name' => $contactName,
            ],
            [
                'job_title' => $jobTitle,
                'phone' => $phone,
            ]
        );
    }

    public function startRow(): int
    {
        return 2;
    }
}
