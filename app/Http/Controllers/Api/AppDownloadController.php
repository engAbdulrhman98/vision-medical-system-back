<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class AppDownloadController extends Controller
{
    public function getAppInfo()
    {
        $settings = Setting::pluck('value', 'key')->toArray();

        $getSetting = fn($key, $default = '') => $settings[$key] ?? $default;

        $androidUrl = $getSetting('app_android_url', url('/api/app/download/apk'));
        $iosUrl = $getSetting('app_ios_url', 'https://apps.apple.com');
        $version = $getSetting('app_version', 'v2.5.2');
        $releaseNotes = $getSetting('app_release_notes', 'تحديث واجهة عروض الأسعار والفواتير الميدانية والتكامل المباشر مع نظام فيجن ميديكال.');

        $apkPath = public_path('downloads/vision-medical.apk');
        $fileSize = '52.6 MB';
        $updatedAt = date('Y-m-d');

        if (file_exists($apkPath)) {
            $bytes = filesize($apkPath);
            $mb = round($bytes / 1024 / 1024, 1);
            $fileSize = $mb . ' MB';
            $updatedAt = date('Y-m-d', filemtime($apkPath));
        }

        return response()->json([
            'status' => true,
            'app_name' => [
                'ar' => 'تطبيق فيجن ميديكال الذكي',
                'en' => 'Vision Medical Mobile App'
            ],
            'version' => $version,
            'release_notes' => $releaseNotes,
            'file_size' => $fileSize,
            'updated_at' => $updatedAt,
            'android_download_url' => $androidUrl,
            'ios_download_url' => $iosUrl,
            'direct_apk_url' => url('/api/app/download/apk'),
            'qr_code_data' => $androidUrl,
            'features' => [
                [
                    'icon' => 'fa-screwdriver-wrench',
                    'title' => [
                        'ar' => 'متابعة المهام الميدانية والزيارات',
                        'en' => 'Field Tasks & Visits'
                    ],
                    'description' => [
                        'ar' => 'استلام وتحديث مهام الصيانة والمبيعات بجميع مواقع العملاء مباشرة.',
                        'en' => 'Receive & update maintenance and sales visits on site.'
                    ]
                ],
                [
                    'icon' => 'fa-file-invoice',
                    'title' => [
                        'ar' => 'عروض الأسعار والطلب الميداني',
                        'en' => 'Field Quotations & Orders'
                    ],
                    'description' => [
                        'ar' => 'طلب وتجهيز عروض الأسعار والفواتير للمحاسب فورياً أثناء التواجد بالميدان.',
                        'en' => 'Request & issue field quotations and invoices to accounting instantly.'
                    ]
                ],
                [
                    'icon' => 'fa-bell',
                    'title' => [
                        'ar' => 'إشعارات وبث مباشر',
                        'en' => 'Real-time Push Notifications'
                    ],
                    'description' => [
                        'ar' => 'التنبيه الفوري بالتكليفات الجغرافية وتحديثات العملاء.',
                        'en' => 'Instant alerts for assigned tasks and updates.'
                    ]
                ],
                [
                    'icon' => 'fa-location-dot',
                    'title' => [
                        'ar' => 'تتبع الخرائط والتقسيم الجغرافي',
                        'en' => 'GPS & Location Integration'
                    ],
                    'description' => [
                        'ar' => 'تحديد موقع العميل والوصول السريع عبر خرائط جوجل.',
                        'en' => 'Locate clients and route quickly via Google Maps.'
                    ]
                ]
            ]
        ])->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
          ->header('Pragma', 'no-cache')
          ->header('Expires', '0');
    }

    public function downloadApk()
    {
        $apkPath = public_path('downloads/vision-medical.apk');

        $settings = Setting::pluck('value', 'key')->toArray();
        $version = $settings['app_version'] ?? 'v2.5.2';

        if (file_exists($apkPath)) {
            return response()->download($apkPath, "vision-medical-{$version}.apk", [
                'Content-Type' => 'application/vnd.android.package-archive',
                'Cache-Control' => 'no-cache, no-store, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
        }

        return response()->json([
            'message' => 'جاري تجهيز الإصدار الأحدث من تطبيق فيجن ميديكال...',
            'version' => $version,
            'download_url' => url('/api/app-download'),
            'file_name' => "vision-medical-{$version}.apk"
        ]);
    }
}
