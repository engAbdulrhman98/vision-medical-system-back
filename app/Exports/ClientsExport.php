<?php

namespace App\Exports;

use App\Models\Client;
use App\Models\ClientContact;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ClientsExport implements WithMultipleSheets
{
    /**
     * @return array
     */
    public function sheets(): array
    {
        return [
            'العملاء' => new ClientsSheetExport(),
            'المسئولين' => new ClientContactsSheetExport(),
        ];
    }
}

class ClientsSheetExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithEvents
{
    public function collection(): Collection
    {
        return Client::with('area')->get();
    }

    public function title(): string
    {
        return 'العملاء';
    }

    public function headings(): array
    {
        return [
            'كود العميل',
            'العميل',
            'نوع العميل',
            'المحافظه',
            'المدينه',
            'المنطقه/المركز',
            'العنوان بالتفصيل',
            'رقم الهاتف',
        ];
    }

    /**
     * @param Client $client
     * @return array
     */
    public function map($client): array
    {
        return [
            (string) $client->id,
            $this->stringify($client->getTranslation('name', 'ar') ?: $client->name),
            $this->stringify($client->type ?: 'غير محدد'),
            $this->stringify($client->governorate ?: 'غير معروف'),
            $this->stringify($client->city ?: 'غير معروف'),
            $this->stringify($client->area ? ($client->area->getTranslation('name', 'ar') ?: $client->area->name) : 'غير معروف'),
            $this->stringify($client->detailed_address ?: ($client->getTranslation('address', 'ar') ?: $client->address) ?: 'غير معروف'),
            $this->stringify($client->phone ?: 'غير معروف'),
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->setRightToLeft(true);
            },
        ];
    }

    private function stringify($val): string
    {
        if (is_null($val)) {
            return '';
        }
        if (is_string($val) || is_numeric($val)) {
            return (string) $val;
        }
        if (is_array($val)) {
            return (string) ($val['ar'] ?? $val['en'] ?? (reset($val) ?: ''));
        }
        return (string) $val;
    }
}

class ClientContactsSheetExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithEvents
{
    public function collection(): Collection
    {
        return ClientContact::with('client')->get();
    }

    public function title(): string
    {
        return 'المسئولين';
    }

    public function headings(): array
    {
        return [
            'كود العميل',
            'اسم العميل',
            'اسم المسئول',
            'الوظيفة / الصفة',
            'رقم الهاتف',
        ];
    }

    /**
     * @param ClientContact $contact
     * @return array
     */
    public function map($contact): array
    {
        $clientName = '';
        if ($contact->client) {
            $clientName = $contact->client->getTranslation('name', 'ar') ?: $contact->client->name;
        }

        return [
            (string) $contact->client_id,
            $this->stringify($clientName),
            $this->stringify($contact->name),
            $this->stringify($contact->job_title),
            $this->stringify($contact->phone),
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->setRightToLeft(true);
            },
        ];
    }

    private function stringify($val): string
    {
        if (is_null($val)) {
            return '';
        }
        if (is_string($val) || is_numeric($val)) {
            return (string) $val;
        }
        if (is_array($val)) {
            return (string) ($val['ar'] ?? $val['en'] ?? (reset($val) ?: ''));
        }
        return (string) $val;
    }
}
