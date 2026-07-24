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
        $version = $getSetting('app_version', 'v2.4.0');
        $releaseNotes = $getSetting('app_release_notes', 'تحديث واجهة المهام الميدانية والتكامل السريع مع نظام فيجن ميديكال.');

        return response()->json([
            'status' => true,
            'app_name' => [
                'ar' => 'تطبيق فيجن ميديكال الذكي',
                'en' => 'Vision Medical Mobile App'
            ],
            'version' => $version,
            'release_notes' => $releaseNotes,
            'file_size' => '28.4 MB',
            'updated_at' => '2026-07-24',
            'android_download_url' => $androidUrl,
            'ios_download_url' => $iosUrl,
            'direct_apk_url' => url('/api/app/download/apk'),
            'qr_code_data' => $androidUrl,
            'features' => [
                [
                    'icon' => 'fa-screwdriver-wrench',
                    'title' => [
                        'ar' => 'متابعة المهام الميدانية',
                        'en' => 'Field Tasks Tracking'
                    ],
                    'description' => [
                        'ar' => 'استلام وتحديث مهام الصيانة والمبيعات في موقع العميل مباشرة.',
                        'en' => 'Receive & update maintenance and sales visits on site.'
                    ]
                ],
                [
                    'icon' => 'fa-shield-halved',
                    'title' => [
                        'ar' => 'توثيق الزيارات برمز OTP',
                        'en' => 'OTP Visit Verification'
                    ],
                    'description' => [
                        'ar' => 'توثيق الحضور وإتمام الخدمة مع مسؤولي المستشفيات بأمان.',
                        'en' => 'Securely verify attendance and service with hospital staff.'
                    ]
                ],
                [
                    'icon' => 'fa-bell',
                    'title' => [
                        'ar' => 'إشعارات فورية وبث مباشر',
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
        ])->header('Cache-Control', 'public, max-age=3600');
    }

    public function downloadApk()
    {
        $apkPath = public_path('downloads/vision-medical.apk');

        if (file_exists($apkPath)) {
            return response()->download($apkPath, 'vision-medical-v2.4.0.apk', [
                'Content-Type' => 'application/vnd.android.package-archive',
            ]);
        }

        // Fallback response with download link info
        return response()->json([
            'message' => 'جاري تجهيز الإصدار الأحدث من تطبيق فيجن ميديكال...',
            'version' => 'v2.4.0',
            'download_url' => url('/api/app-download'),
            'file_name' => 'vision-medical-v2.4.0.apk'
        ]);
    }
}
