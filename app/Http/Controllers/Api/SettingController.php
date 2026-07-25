<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    private array $dayKeys = ['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

    public function index()
    {
        /** @var array<string,string> $s */
        $s = Setting::pluck('value', 'key')->toArray();

        $g = fn(string $key, string $default = '') => $s[$key] ?? $default;

        // Build per-day working hours structured data
        $workingHoursDays = [];
        foreach ($this->dayKeys as $day) {
            $workingHoursDays[$day] = [
                'open' => $g("day_{$day}_open", $day === 'friday' ? '0' : '1'),
                'from' => $g("day_{$day}_from", '08:00'),
                'to'   => $g("day_{$day}_to",   '17:00'),
            ];
        }

        return response()->json([
            'store_name' => [
                'ar' => $g('store_name_ar', 'فيجن ميديكال للأجهزة الطبية'),
                'en' => $g('store_name_en', 'Vision Medical for Devices'),
            ],
            'store_email'          => $g('store_email', 'info@vision-medical.com'),
            'store_phone'          => $g('store_phone', '+20 100 123 4567'),
            'whatsapp'             => $g('whatsapp', '201001234567'),
            'maintenance_phone'    => $g('maintenance_phone', '+20 111 765 4321'),
            'maintenance_whatsapp' => $g('maintenance_whatsapp', '201117654321'),
            'about_us_title' => [
                'ar' => $g('about_us_title_ar', 'من نحن - فيجن ميديكال'),
                'en' => $g('about_us_title_en', 'About Us - Vision Medical'),
            ],
            'about_us_content' => [
                'ar' => $g('about_us_content_ar'),
                'en' => $g('about_us_content_en'),
            ],
            'footer_text' => [
                'ar' => $g('footer_text_ar', 'جميع الحقوق محفوظة © فيجن ميديكال 2026.'),
                'en' => $g('footer_text_en', 'All rights reserved © Vision Medical 2026.'),
            ],
            'company_map_link'   => $g('company_map_link'),
            'app_android_url'    => $g('app_android_url', url('/api/app/download/apk')),
            'app_ios_url'        => $g('app_ios_url', 'https://apps.apple.com'),
            'app_version'        => $g('app_version', 'v2.5.2'),
            'app_release_notes'  => $g('app_release_notes', 'تحديث واجهة عروض الأسعار والفواتير الميدانية والتكامل المباشر مع نظام فيجن ميديكال.'),
            'working_hours_days' => $workingHoursDays,
        ])->header('Cache-Control', 'private, no-cache');
    }

    public function update(Request $request)
    {
        // Decode JSON body manually if needed — supports both JSON and form-encoded
        $data = $request->all();

        // ── Validate ─────────────────────────────────────────────────────────
        $request->validate([
            'store_name'           => 'required|array',
            'store_name.ar'        => 'required|string|max:255',
            'store_name.en'        => 'required|string|max:255',
            'store_email'          => 'required|email|max:255',
            'store_phone'          => 'required|string|max:50',
            'whatsapp'             => 'required|string|max:50',
            'maintenance_phone'    => 'nullable|string|max:50',
            'maintenance_whatsapp' => 'nullable|string|max:50',
            'about_us_title'       => 'required|array',
            'about_us_title.ar'    => 'required|string|max:255',
            'about_us_title.en'    => 'required|string|max:255',
            'about_us_content'     => 'required|array',
            'about_us_content.ar'  => 'required|string',
            'about_us_content.en'  => 'required|string',
            'footer_text'          => 'nullable|array',
            'footer_text.ar'       => 'nullable|string|max:255',
            'footer_text.en'       => 'nullable|string|max:255',
            'company_map_link'     => 'nullable|string|max:2000',
            'app_android_url'      => 'nullable|string|max:2000',
            'app_ios_url'          => 'nullable|string|max:2000',
            'app_version'          => 'nullable|string|max:50',
            'app_release_notes'    => 'nullable|string|max:1000',
            'working_hours_days'   => 'nullable|array',
        ]);

        // ── Read from $data array (works regardless of Content-Type) ──────────
        $storeName       = $data['store_name']       ?? [];
        $aboutUsTitle    = $data['about_us_title']   ?? [];
        $aboutUsContent  = $data['about_us_content'] ?? [];
        $footerText      = $data['footer_text']      ?? [];

        Setting::setValue('store_name_ar', $storeName['ar'] ?? '');
        Setting::setValue('store_name_en', $storeName['en'] ?? '');
        Setting::setValue('store_email', $data['store_email'] ?? '');
        Setting::setValue('store_phone', $data['store_phone'] ?? '');
        Setting::setValue('whatsapp', preg_replace('/[^0-9]/', '', $data['whatsapp'] ?? ''));
        Setting::setValue('maintenance_phone', $data['maintenance_phone'] ?? '');
        Setting::setValue('maintenance_whatsapp', preg_replace('/[^0-9]/', '', $data['maintenance_whatsapp'] ?? ''));
        Setting::setValue('about_us_title_ar', $aboutUsTitle['ar'] ?? '');
        Setting::setValue('about_us_title_en', $aboutUsTitle['en'] ?? '');
        Setting::setValue('about_us_content_ar', $aboutUsContent['ar'] ?? '');
        Setting::setValue('about_us_content_en', $aboutUsContent['en'] ?? '');
        Setting::setValue('footer_text_ar', $footerText['ar'] ?? '');
        Setting::setValue('footer_text_en', $footerText['en'] ?? '');
        Setting::setValue('company_map_link', $data['company_map_link'] ?? '');
        Setting::setValue('app_android_url', $data['app_android_url'] ?? '');
        Setting::setValue('app_ios_url', $data['app_ios_url'] ?? '');
        Setting::setValue('app_version', $data['app_version'] ?? 'v2.5.2');
        Setting::setValue('app_release_notes', $data['app_release_notes'] ?? '');

        // Save per-day working hours
        $days = $data['working_hours_days'] ?? [];
        foreach ($this->dayKeys as $day) {
            $dayData = $days[$day] ?? [];
            $isOpen  = isset($dayData['open']) ? (filter_var($dayData['open'], FILTER_VALIDATE_BOOLEAN) ? '1' : '0') : ($day === 'friday' ? '0' : '1');
            Setting::setValue("day_{$day}_open", $isOpen);
            Setting::setValue("day_{$day}_from", $dayData['from'] ?? '08:00');
            Setting::setValue("day_{$day}_to",   $dayData['to']   ?? '17:00');
        }

        activity()
            ->causedBy(auth()->user())
            ->log('تم تحديث إعدادات الموقع العامة ومواعيد العمل');

        return response()->json(['message' => 'Settings updated successfully']);
    }
}
