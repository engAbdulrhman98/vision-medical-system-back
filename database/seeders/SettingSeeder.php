<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            'store_name' => 'فيجن ميديكال | Vision Medical',
            'store_name_ar' => 'فيجن ميديكال للأجهزة الطبية',
            'store_name_en' => 'Vision Medical for Devices',
            'store_email' => 'info@vision-medical.com',
            'store_phone' => '+20 100 123 4567',
            'whatsapp' => '201001234567', // WhatsApp format without +
            'maintenance_phone' => '+20 111 765 4321',
            'maintenance_whatsapp' => '201117654321', // WhatsApp format without +
            'about_us_title' => 'من نحن - فيجن ميديكال',
            'about_us_title_ar' => 'من نحن - فيجن ميديكال',
            'about_us_title_en' => 'About Us - Vision Medical',
            'about_us_content' => 'نحن في فيجن ميديكال (Vision Medical) نوفر أحدث وأجود الأجهزة والمستلزمات الطبية للعيادات والمستشفيات والاستخدام المنزلي. نلتزم بأعلى معايير الجودة العالمية ونطمح لتسهيل الحصول على الرعاية الصحية المتكاملة لجميع عملائنا من خلال تقديم منتجات معتمدة وموثوقة بنسبة 100%. تأسست شركتنا لتكون الشريك الأول في توفير الحلول الطبية المتقدمة.',
            'about_us_content_ar' => 'نحن في فيجن ميديكال (Vision Medical) نوفر أحدث وأجود الأجهزة والمستلزمات الطبية للعيادات والمستشفيات والاستخدام المنزلي. نلتزم بأعلى معايير الجودة العالمية ونطمح لتسهيل الحصول على الرعاية الصحية المتكاملة لجميع عملائنا من خلال تقديم منتجات معتمدة وموثوقة بنسبة 100%. تأسست شركتنا لتكون الشريك الأول في توفير الحلول الطبية المتقدمة.',
            'about_us_content_en' => 'We at Vision Medical provide the latest and finest medical equipment and supplies for clinics, hospitals, and home use. We commit to the highest international quality standards and aspire to facilitate integrated healthcare for all our clients by providing 100% certified and reliable products.',
            'footer_text' => 'جميع الحقوق محفوظة © فيجن ميديكال 2026. نسعى دائماً لتقديم الأفضل لصحتكم.',
            'footer_text_ar' => 'جميع الحقوق محفوظة © فيجن ميديكال 2026. نسعى دائماً لتقديم الأفضل لصحتكم.',
            'footer_text_en' => 'All rights reserved © Vision Medical 2026. We always strive to provide the best for your health.',
            'company_map_link' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d110502.61186196237!2d31.188339176313795!3d30.059483810452335!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14583fa60b21beeb%3A0x79dfb296e84d3b7d!2sCairo%2C%20Cairo%20Governorate%2C%20Egypt!5e0!3m2!1sen!2seg!4v1717540000000!5m2!1sen!2seg',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
