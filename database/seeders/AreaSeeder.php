<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $governorates = [
            [
                'name' => ['ar' => 'القاهرة', 'en' => 'Cairo'],
                'cities' => [
                    ['ar' => 'مدينة نصر', 'en' => 'Nasr City'],
                    ['ar' => 'مصر الجديدة', 'en' => 'Heliopolis'],
                    ['ar' => 'المعادي', 'en' => 'Maadi'],
                    ['ar' => 'القاهرة الجديدة', 'en' => 'New Cairo'],
                    ['ar' => 'شبرا', 'en' => 'Shoubra'],
                    ['ar' => 'حلوان', 'en' => 'Helwan'],
                ]
            ],
            [
                'name' => ['ar' => 'الجيزة', 'en' => 'Giza'],
                'cities' => [
                    ['ar' => 'الهرم', 'en' => 'Haram'],
                    ['ar' => 'الدقي', 'en' => 'Dokki'],
                    ['ar' => 'المهندسين', 'en' => 'Mohandessin'],
                    ['ar' => 'السادس من أكتوبر', 'en' => '6th of October'],
                    ['ar' => 'الشيخ زايد', 'en' => 'Sheikh Zayed'],
                    ['ar' => 'العجوزة', 'en' => 'Agouza'],
                    ['ar' => 'فيصل', 'en' => 'Faisal'],
                ]
            ],
            [
                'name' => ['ar' => 'الإسكندرية', 'en' => 'Alexandria'],
                'cities' => [
                    ['ar' => 'سيدي جابر', 'en' => 'Sidi Gaber'],
                    ['ar' => 'سموحة', 'en' => 'Smouha'],
                    ['ar' => 'الرمل', 'en' => 'El Raml'],
                    ['ar' => 'المنتزة', 'en' => 'Montaza'],
                    ['ar' => 'العجمي', 'en' => 'Ajami'],
                ]
            ],
            [
                'name' => ['ar' => 'القليوبية', 'en' => 'Qalyubia'],
                'cities' => [
                    ['ar' => 'بنها', 'en' => 'Banha'],
                    ['ar' => 'شبرا الخيمة', 'en' => 'Shubra El Kheima'],
                    ['ar' => 'الخانكة', 'en' => 'Khanka'],
                    ['ar' => 'قليوب', 'en' => 'Qalyub'],
                ]
            ],
            [
                'name' => ['ar' => 'بورسعيد', 'en' => 'Port Said'],
                'cities' => [
                    ['ar' => 'بورفؤاد', 'en' => 'Bourfouad'],
                    ['ar' => 'حي الشرق', 'en' => 'El Sharq District'],
                    ['ar' => 'حي العرب', 'en' => 'El Arab District'],
                ]
            ],
            [
                'name' => ['ar' => 'السويس', 'en' => 'Suez'],
                'cities' => [
                    ['ar' => 'حي الأربعين', 'en' => 'Arbaeen District'],
                    ['ar' => 'بور توفيق', 'en' => 'Port Tewfik'],
                    ['ar' => 'حي الجناين', 'en' => 'Ganayen District'],
                ]
            ],
            [
                'name' => ['ar' => 'دمياط', 'en' => 'Damietta'],
                'cities' => [
                    ['ar' => 'دمياط الجديدة', 'en' => 'New Damietta'],
                    ['ar' => 'رأس البر', 'en' => 'Ras El Bar'],
                    ['ar' => 'فارسكور', 'en' => 'Faraskur'],
                ]
            ],
            [
                'name' => ['ar' => 'الدقهلية', 'en' => 'Dakahlia'],
                'cities' => [
                    ['ar' => 'المنصورة', 'en' => 'Mansoura'],
                    ['ar' => 'ميت غمر', 'en' => 'Mit Ghamr'],
                    ['ar' => 'طلخا', 'en' => 'Talkha'],
                    ['ar' => 'السنبلاوين', 'en' => 'Senbellawein'],
                ]
            ],
            [
                'name' => ['ar' => 'الشرقية', 'en' => 'Sharkia'],
                'cities' => [
                    ['ar' => 'الزقازيق', 'en' => 'Zagazig'],
                    ['ar' => 'العاشر من رمضان', 'en' => '10th of Ramadan'],
                    ['ar' => 'بلبيس', 'en' => 'Belbeis'],
                    ['ar' => 'فاقوس', 'en' => 'Faqous'],
                ]
            ],
            [
                'name' => ['ar' => 'الغربية', 'en' => 'Gharbia'],
                'cities' => [
                    ['ar' => 'طنطا', 'en' => 'Tanta'],
                    ['ar' => 'المحلة الكبرى', 'en' => 'El Mahalla El Kubra'],
                    ['ar' => 'كفر الزيات', 'en' => 'Kafr El Zayat'],
                    ['ar' => 'زفتى', 'en' => 'Zefta'],
                ]
            ],
            [
                'name' => ['ar' => 'المنوفية', 'en' => 'Monufia'],
                'cities' => [
                    ['ar' => 'شبين الكوم', 'en' => 'Shibin El Kom'],
                    ['ar' => 'مدينة السادات', 'en' => 'Sadat City'],
                    ['ar' => 'منوف', 'en' => 'Menouf'],
                    ['ar' => 'أشمون', 'en' => 'Ashmoun'],
                ]
            ],
            [
                'name' => ['ar' => 'البحيرة', 'en' => 'Beheira'],
                'cities' => [
                    ['ar' => 'دمنهور', 'en' => 'Damanhour'],
                    ['ar' => 'كفر الدوار', 'en' => 'Kafr El Dawwar'],
                    ['ar' => 'كوم حمادة', 'en' => 'Kom Hamada'],
                    ['ar' => 'إدكو', 'en' => 'Edku'],
                ]
            ],
            [
                'name' => ['ar' => 'الإسماعيلية', 'en' => 'Ismailia'],
                'cities' => [
                    ['ar' => 'فايد', 'en' => 'Fayed'],
                    ['ar' => 'القنطرة شرق', 'en' => 'Qantara East'],
                    ['ar' => 'القنطرة غرب', 'en' => 'Qantara West'],
                ]
            ],
            [
                'name' => ['ar' => 'بني سويف', 'en' => 'Beni Suef'],
                'cities' => [
                    ['ar' => 'ببا', 'en' => 'Biba'],
                    ['ar' => 'ناصر', 'en' => 'Nasser'],
                    ['ar' => 'الفشن', 'en' => 'Fashn'],
                ]
            ],
            [
                'name' => ['ar' => 'الفيوم', 'en' => 'Fayoum'],
                'cities' => [
                    ['ar' => 'سنورس', 'en' => 'Sinnuris'],
                    ['ar' => 'إبشواي', 'en' => 'Ibshaway'],
                    ['ar' => 'طامية', 'en' => 'Tamia'],
                ]
            ],
            [
                'name' => ['ar' => 'المنيا', 'en' => 'Minya'],
                'cities' => [
                    ['ar' => 'ملوي', 'en' => 'Mallawi'],
                    ['ar' => 'سمالوط', 'en' => 'Samalut'],
                    ['ar' => 'بني مزار', 'en' => 'Beni Mazar'],
                    ['ar' => 'مغاغة', 'en' => 'Maghagha'],
                ]
            ],
            [
                'name' => ['ar' => 'أسيوط', 'en' => 'Assiut'],
                'cities' => [
                    ['ar' => 'ديروط', 'en' => 'Dairut'],
                    ['ar' => 'أبنوب', 'en' => 'Abnoub'],
                    ['ar' => 'منفلوط', 'en' => 'Manfalut'],
                    ['ar' => 'أبو تيج', 'en' => 'Abu Tig'],
                ]
            ],
            [
                'name' => ['ar' => 'سوهاج', 'en' => 'Sohag'],
                'cities' => [
                    ['ar' => 'جرجا', 'en' => 'Girga'],
                    ['ar' => 'أخميم', 'en' => 'Akhmim'],
                    ['ar' => 'طهطا', 'en' => 'Tahta'],
                    ['ar' => 'البلينا', 'en' => 'Balyana'],
                ]
            ],
            [
                'name' => ['ar' => 'قنا', 'en' => 'Qena'],
                'cities' => [
                    ['ar' => 'نجع حمادي', 'en' => 'Nag Hammadi'],
                    ['ar' => 'دشنا', 'en' => 'Deshna'],
                    ['ar' => 'قوص', 'en' => 'Qus'],
                    ['ar' => 'أبو تشت', 'en' => 'Abu Tasht'],
                ]
            ],
            [
                'name' => ['ar' => 'الأقصر', 'en' => 'Luxor'],
                'cities' => [
                    ['ar' => 'إسنا', 'en' => 'Esna'],
                    ['ar' => 'أرمنت', 'en' => 'Armant'],
                    ['ar' => 'القرنة', 'en' => 'Gourna'],
                ]
            ],
            [
                'name' => ['ar' => 'أسوان', 'en' => 'Aswan'],
                'cities' => [
                    ['ar' => 'كوم أمبو', 'en' => 'Kom Ombo'],
                    ['ar' => 'إدفو', 'en' => 'Edfu'],
                    ['ar' => 'نصر النوبة', 'en' => 'Nasr Nuba'],
                ]
            ],
            [
                'name' => ['ar' => 'البحر الأحمر', 'en' => 'Red Sea'],
                'cities' => [
                    ['ar' => 'الغردقة', 'en' => 'Hurghada'],
                    ['ar' => 'سفاجا', 'en' => 'Safaga'],
                    ['ar' => 'مرسى علم', 'en' => 'Marsa Alam'],
                    ['ar' => 'الجونة', 'en' => 'Gouna'],
                ]
            ],
            [
                'name' => ['ar' => 'الوادي الجديد', 'en' => 'New Valley'],
                'cities' => [
                    ['ar' => 'الخارجة', 'en' => 'Kharga'],
                    ['ar' => 'الداخلة', 'en' => 'Dakhla'],
                    ['ar' => 'الفرافرة', 'en' => 'Farafra'],
                ]
            ],
            [
                'name' => ['ar' => 'مطروح', 'en' => 'Matrouh'],
                'cities' => [
                    ['ar' => 'مرسى مطروح', 'en' => 'Marsa Matrouh'],
                    ['ar' => 'سيوة', 'en' => 'Siwa'],
                    ['ar' => 'العلمين', 'en' => 'El Alamein'],
                ]
            ],
            [
                'name' => ['ar' => 'شمال سيناء', 'en' => 'North Sinai'],
                'cities' => [
                    ['ar' => 'العريش', 'en' => 'Arish'],
                    ['ar' => 'الشيخ زويد', 'en' => 'Sheikh Zuweid'],
                    ['ar' => 'رفح', 'en' => 'Rafah'],
                ]
            ],
            [
                'name' => ['ar' => 'جنوب سيناء', 'en' => 'South Sinai'],
                'cities' => [
                    ['ar' => 'شرم الشيخ', 'en' => 'Sharm El Sheikh'],
                    ['ar' => 'دهب', 'en' => 'Dahab'],
                    ['ar' => 'نويبع', 'en' => 'Nuweiba'],
                    ['ar' => 'الطور', 'en' => 'Tor'],
                ]
            ],
            [
                'name' => ['ar' => 'كفر الشيخ', 'en' => 'Kafr El Sheikh'],
                'cities' => [
                    ['ar' => 'دسوق', 'en' => 'Desouk'],
                    ['ar' => 'مطوبس', 'en' => 'Metoubas'],
                    ['ar' => 'بلطيم', 'en' => 'Baltim'],
                ]
            ],
        ];

        foreach ($governorates as $govData) {
            // Seed the Governorate
            $gov = Area::create([
                'name' => [
                    'ar' => $govData['name']['ar'],
                    'en' => $govData['name']['en']
                ],
                'type' => 'governorate',
                'parent_id' => null,
            ]);

            // Seed the associated Cities under this Governorate
            foreach ($govData['cities'] as $cityData) {
                Area::create([
                    'name' => $cityData,
                    'type' => 'city',
                    'parent_id' => $gov->id,
                ]);
            }
        }
    }
}
