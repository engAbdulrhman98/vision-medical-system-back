<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Review;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();
        $brands = Brand::all();

        if ($categories->isEmpty() || $brands->isEmpty()) {
            return;
        }

        $items = [
            ['name_ar' => 'جهاز قياس ضغط الدم Omron M3', 'name_en' => 'Omron M3 Blood Pressure Monitor', 'price' => 2990, 'slug' => 'omron-m3-bpm'],
            ['name_ar' => 'جهاز قياس نسبة السكر Beurer GL50', 'name_en' => 'Beurer GL50 Glucose Monitor', 'price' => 1890, 'slug' => 'beurer-gl50-monitor'],
            ['name_ar' => 'جهاز نيبولايزر استنشاق البخار Philips', 'name_en' => 'Philips Innospire Nebulizer', 'price' => 3500, 'slug' => 'philips-nebulizer'],
            ['name_ar' => 'علبة كمامات طبية 3 طبقات 50 حبة', 'name_en' => '3-Ply Medical Masks 50pcs', 'price' => 250, 'slug' => '3ply-masks-50pcs'],
            ['name_ar' => 'جهاز فحص ضغط العين الرقمي', 'name_en' => 'Digital Eye Tonometer', 'price' => 45000, 'slug' => 'digital-eye-tonometer'],
            ['name_ar' => 'جهاز موجات فوق صوتية سونار محمول', 'name_en' => 'Portable Ultrasound System', 'price' => 125000, 'slug' => 'portable-ultrasound-sys'],
            ['name_ar' => 'جهاز تخطيط القلب ECG 12 القناة', 'name_en' => '12-Channel ECG Machine', 'price' => 38000, 'slug' => '12ch-ecg-machine'],
            ['name_ar' => 'جهاز صدمات قلبية Defibrillator مع مونيتر', 'name_en' => 'Defibrillator with Monitor Unit', 'price' => 85000, 'slug' => 'defibrillator-monitor-unit'],
            ['name_ar' => 'جهاز تنفس اصطناعي للعناية المركزة ICU', 'name_en' => 'ICU Medical Ventilator', 'price' => 240000, 'slug' => 'icu-medical-ventilator'],
            ['name_ar' => 'طلمبة ضخ المحاليل الدقيقة Infusion Pump', 'name_en' => 'Precision Medical Infusion Pump', 'price' => 16500, 'slug' => 'precision-infusion-pump'],
            ['name_ar' => 'شاشة مراقبة العلامات الحيوية Patient Monitor', 'name_en' => 'Multi-Parameter Patient Monitor', 'price' => 32000, 'slug' => 'multiparameter-patient-monitor'],
            ['name_ar' => 'حاضنة بوث للمبتسرين وحفظ حديثي الولادة', 'name_en' => 'Infant Incubator Unit', 'price' => 110000, 'slug' => 'infant-incubator-unit'],
            ['name_ar' => 'جهاز كي وجراحة الكتروني Electrosurgical Unit', 'name_en' => 'Electrosurgical ESU Generator', 'price' => 52000, 'slug' => 'electrosurgical-esu-generator'],
            ['name_ar' => 'مصباح سقف لغرفة العمليات LED Surgical Light', 'name_en' => 'LED Ceiling Surgical Shadowless Light', 'price' => 68000, 'slug' => 'led-surgical-shadowless-light'],
            ['name_ar' => 'جهاز قياس الأكسجين بالدم Pulse Oximeter', 'name_en' => 'Finger Pulse Oximeter', 'price' => 850, 'slug' => 'finger-pulse-oximeter'],
            ['name_ar' => 'سماعة طبية احترافية Prestige Stethoscope', 'name_en' => 'Prestige Professional Stethoscope', 'price' => 1400, 'slug' => 'prestige-stethoscope'],
            ['name_ar' => 'جهاز تخدير متكامل مع وحدة غازات', 'name_en' => 'Complete Anesthesia Workstation', 'price' => 280000, 'slug' => 'anesthesia-workstation'],
            ['name_ar' => 'جهاز أوتوكلاف تعقيم بالبخار 50 ليتر', 'name_en' => 'Steam Autoclave Sterilizer 50L', 'price' => 75000, 'slug' => 'autoclave-sterilizer-50l'],
            ['name_ar' => 'منظار ألياف ضوئية للجهاز الهضمي', 'name_en' => 'Gastrointestinal Endoscopy Probe', 'price' => 190000, 'slug' => 'gastrointestinal-endoscopy-probe'],
            ['name_ar' => 'جهاز فحص فحص القاع والشبكية للعيون', 'name_en' => 'Digital Ophthalmoscope Scanner', 'price' => 42000, 'slug' => 'digital-ophthalmoscope-scanner'],
        ];

        foreach ($items as $idx => $item) {
            $cat = $categories[$idx % $categories->count()];
            $brd = $brands[$idx % $brands->count()];

            $product = Product::where('slug', $item['slug'])->first();
            if (!$product) {
                $product = new Product();
                $product->slug = $item['slug'];
            }

            $product->price = $item['price'];
            $product->image = '/images/products/med_prod_' . ($idx + 1) . '.png';
            $product->category_id = $cat->id;
            $product->brand_id = $brd->id;
            $product->in_stock = true;

            $product->setTranslation('name', 'ar', $item['name_ar']);
            $product->setTranslation('name', 'en', $item['name_en']);
            $product->setTranslation('description', 'ar', 'جهاز ومعدة طبية عالية الكفاءة معتمدة للعيادات والمستشفيات ' . $item['name_ar']);
            $product->setTranslation('description', 'en', 'High efficiency certified medical equipment ' . $item['name_en']);
            $product->setTranslation('details', 'ar', "المواصفات الفنية:\n- ضمان شامل لمدة 24 شهر مع عقود صيانة.\n- مطابقة للمعايير الدولية للسلامة والجودة.");
            $product->setTranslation('details', 'en', "Technical Details:\n- 24 Months warranty with maintenance support.\n- Complies with ISO and CE medical standards.");
            $product->save();

            // Seed reviews for each product
            Review::updateOrCreate(
                ['product_id' => $product->id, 'reviewer_name' => 'د. أحمد العتيبي'],
                [
                    'rating' => 5,
                    'comment' => 'منتج مالي وممتاز جداً، ودقة متناهية في القياس والاستخدام.',
                    'is_approved' => true,
                    'created_at' => now()->subDays(rand(1, 15))
                ]
            );

            Review::updateOrCreate(
                ['product_id' => $product->id, 'reviewer_name' => 'م. مريم حسن'],
                [
                    'rating' => 4,
                    'comment' => 'الجهاز مطابق للطلب وتمت المعايرة والتسليم بنجاح.',
                    'is_approved' => true,
                    'created_at' => now()->subDays(rand(1, 10))
                ]
            );
        }
    }
}
