<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Client;
use App\Models\ClientContact;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = Area::all();
        $fallbackArea = $areas->first() ? $areas->first()->id : 1;

        $clientsData = [
            [
                'name' => ['ar' => 'عيادة مدينة نصر التخصصية', 'en' => 'Nasr City Specialized Clinic'],
                'type' => 'عيادة',
                'governorate' => 'القاهرة',
                'city' => 'مدينة نصر',
                'detailed_address' => 'شارع الطيران، بجوار تقاطع مصطفى النحاس، مدينة نصر',
                'area_search' => 'Nasr City',
                'address' => ['ar' => 'شارع الطيران، مدينة نصر، القاهرة', 'en' => 'Tayaran St, Nasr City, Cairo'],
                'phone' => '0223456789',
                'contacts' => [
                    ['name' => 'د. محمد صلاح', 'phone' => '01012345671', 'job_title' => 'مدير العيادة'],
                    ['name' => 'م. أسامة محمود', 'phone' => '01123456781', 'job_title' => 'مهندس الأجهزة الطبية'],
                ]
            ],
            [
                'name' => ['ar' => 'مستشفى الجيزة الدولي', 'en' => 'Giza International Hospital'],
                'type' => 'مستشفى',
                'governorate' => 'الجيزة',
                'city' => 'الدقي',
                'detailed_address' => '15 شارع التحرير، الدقي، الجيزة',
                'area_search' => 'Giza Governorate',
                'address' => ['ar' => 'شارع التحرير، الدقي، الجيزة', 'en' => 'Tahrir St, Dokki, Giza'],
                'phone' => '0237654321',
                'contacts' => [
                    ['name' => 'د. أحمد فتحي', 'phone' => '01023456782', 'job_title' => 'مدير الصيانة الطبية'],
                    ['name' => 'م. مريم حسن', 'phone' => '01234567892', 'job_title' => 'مهندسة المشتريات والتجهيزات'],
                ]
            ],
            [
                'name' => ['ar' => 'مستشفى أندلسية سموحة', 'en' => 'Andalusia Hospital Smouha'],
                'type' => 'مستشفى',
                'governorate' => 'الإسكندرية',
                'city' => 'سموحة',
                'detailed_address' => 'ميدان فيكتور عمانويل، سموحة، الإسكندرية',
                'area_search' => 'Smouha',
                'address' => ['ar' => 'سموحة، الإسكندرية', 'en' => 'Smouha, Alexandria'],
                'phone' => '034275331',
                'contacts' => [
                    ['name' => 'د. عمرو السعدني', 'phone' => '01034567893', 'job_title' => 'مدير المستشفى'],
                    ['name' => 'م. طارق كمال', 'phone' => '01145678903', 'job_title' => 'مهندس الموقع والشؤون الفنية'],
                ]
            ],
            [
                'name' => ['ar' => 'مركز القاهرة للرنين المغناطيسي', 'en' => 'Cairo MRI & Radiology Center'],
                'type' => 'مركز طبي',
                'governorate' => 'القاهرة',
                'city' => 'المعادي',
                'detailed_address' => '72 شارع 9، المعادي، القاهرة',
                'area_search' => 'Maadi',
                'address' => ['ar' => 'شارع 9، المعادي، القاهرة', 'en' => 'Street 9, Maadi, Cairo'],
                'phone' => '0227543210',
                'contacts' => [
                    ['name' => 'د. خالد عبد العزيز', 'phone' => '01045678904', 'job_title' => 'رئيس قسم الأشعة'],
                    ['name' => 'م. إبراهيم سيد', 'phone' => '01256789014', 'job_title' => 'مهندس الصيانة الوقائية'],
                ]
            ],
            [
                'name' => ['ar' => 'مستشفى السلام الدولي', 'en' => 'Al-Salam International Hospital'],
                'type' => 'مستشفى',
                'governorate' => 'القاهرة',
                'city' => 'المعادي',
                'detailed_address' => 'كورنيش النيل، المعادي، القاهرة',
                'area_search' => 'Maadi',
                'address' => ['ar' => 'كورنيش النيل، المعادي، القاهرة', 'en' => 'Nile Corniche, Maadi, Cairo'],
                'phone' => '0225240250',
                'contacts' => [
                    ['name' => 'د. شريف مصطفى', 'phone' => '01056789015', 'job_title' => 'نائب مدير المستشفى'],
                    ['name' => 'م. مصطفى نور', 'phone' => '01167890125', 'job_title' => 'رئيس قسم الهندسة الطبية'],
                ]
            ],
            [
                'name' => ['ar' => 'مركز النيل للأشعة والتحاليل', 'en' => 'Nile Scan & Labs Center'],
                'type' => 'مركز طبي',
                'governorate' => 'القاهرة',
                'city' => 'مصر الجديدة',
                'detailed_address' => '44 شارع الميرغني، مصر الجديدة، القاهرة',
                'area_search' => 'Heliopolis',
                'address' => ['ar' => 'شارع الميرغني، مصر الجديدة، القاهرة', 'en' => 'El Merghany St, Heliopolis, Cairo'],
                'phone' => '0222918800',
                'contacts' => [
                    ['name' => 'د. نادية رشاد', 'phone' => '01067890126', 'job_title' => 'مديرة المركز'],
                ]
            ],
            [
                'name' => ['ar' => 'مستشفى الشروق التخصصي', 'en' => 'El Shorouk Specialized Hospital'],
                'type' => 'مستشفى',
                'governorate' => 'الجيزة',
                'city' => 'الدقي',
                'detailed_address' => '5 شارع مصدق، الدقي، الجيزة',
                'area_search' => 'Dokki',
                'address' => ['ar' => 'شارع مصدق، الدقي، الجيزة', 'en' => 'Mossadak St, Dokki, Giza'],
                'phone' => '0233360400',
                'contacts' => [
                    ['name' => 'د. ياسر توفيق', 'phone' => '01078901237', 'job_title' => 'مدير العمليات والرعاية'],
                    ['name' => 'م. حاتم نبيل', 'phone' => '01278901237', 'job_title' => 'مهندس الصيانة الداخلية'],
                ]
            ],
            [
                'name' => ['ar' => 'مستشفى دار الفؤاد', 'en' => 'Dar Al Fouad Hospital'],
                'type' => 'مستشفى',
                'governorate' => 'الجيزة',
                'city' => '6 أكتوبر',
                'detailed_address' => 'امتداد محور 26 يوليو، الحي المتميز، 6 أكتوبر، الجيزة',
                'area_search' => '6th of October',
                'address' => ['ar' => 'امتداد محور 26 يوليو، 6 أكتوبر، الجيزة', 'en' => '26th of July Corridor, 6th of October, Giza'],
                'phone' => '0238247000',
                'contacts' => [
                    ['name' => 'د. هشام مجدي', 'phone' => '01089012348', 'job_title' => 'مدير الصيانة والتجهيزات'],
                    ['name' => 'م. عصام فوزي', 'phone' => '01189012348', 'job_title' => 'مهندس الأجهزة الطبية'],
                ]
            ],
            [
                'name' => ['ar' => 'مستشفى كليوباترا', 'en' => 'Cleopatra Hospital'],
                'type' => 'مستشفى',
                'governorate' => 'القاهرة',
                'city' => 'مصر الجديدة',
                'detailed_address' => '39 شارع كليوباترا، ميدان صلاح الدين، مصر الجديدة، القاهرة',
                'area_search' => 'Heliopolis',
                'address' => ['ar' => 'شارع كليوباترا، مصر الجديدة، القاهرة', 'en' => 'Cleopatra St, Heliopolis, Cairo'],
                'phone' => '0224143931',
                'contacts' => [
                    ['name' => 'د. حازم زكي', 'phone' => '01090123459', 'job_title' => 'مدير المستشفى'],
                ]
            ],
            [
                'name' => ['ar' => 'مركز ألفا لجراحة العيون', 'en' => 'Alpha Vision Center'],
                'type' => 'مركز طبي',
                'governorate' => 'القاهرة',
                'city' => 'الزمالك',
                'detailed_address' => '110 شارع 26 يوليو، الزمالك، القاهرة',
                'area_search' => 'Zamalek',
                'address' => ['ar' => 'شارع 26 يوليو، الزمالك، القاهرة', 'en' => '26th of July St, Zamalek, Cairo'],
                'phone' => '0227351234',
                'contacts' => [
                    ['name' => 'د. وائل فاروق', 'phone' => '01001234560', 'job_title' => 'كبير أطباء العيون'],
                    ['name' => 'م. زياد سليم', 'phone' => '01201234560', 'job_title' => 'مسؤول المعايرة والدعم الفني'],
                ]
            ],
            [
                'name' => ['ar' => 'مستشفى المواساة الملكي', 'en' => 'Mouwasat Hospital Alex'],
                'type' => 'مستشفى',
                'governorate' => 'الإسكندرية',
                'city' => 'الشاطبي',
                'detailed_address' => 'شارع الحرية، الشاطبي، الإسكندرية',
                'area_search' => 'Alexandria Governorate',
                'address' => ['ar' => 'شارع الحرية، الشاطبي، الإسكندرية', 'en' => 'Horriya Ave, Shatby, Alexandria'],
                'phone' => '035921000',
                'contacts' => [
                    ['name' => 'د. بسام عبد الله', 'phone' => '01011223344', 'job_title' => 'مدير الصيانة الفنية'],
                ]
            ],
            [
                'name' => ['ar' => 'مستشفى الإسماعيلية التخصصي', 'en' => 'Ismailia Specialized Hospital'],
                'type' => 'مستشفى',
                'governorate' => 'الإسماعيلية',
                'city' => 'الإسماعيلية',
                'detailed_address' => 'طريق الشيخ زايد، الإسماعيلية',
                'area_search' => 'Ismailia Governorate',
                'address' => ['ar' => 'طريق الشيخ زايد، الإسماعيلية', 'en' => 'Sheikh Zayed Rd, Ismailia'],
                'phone' => '0643322110',
                'contacts' => [
                    ['name' => 'د. محمود جابر', 'phone' => '01022334455', 'job_title' => 'مدير القطاع الطبي'],
                    ['name' => 'م. أيمن رمزي', 'phone' => '01122334455', 'job_title' => 'مهندس صيانة أجهزة التخدير'],
                ]
            ],
            [
                'name' => ['ar' => 'مستشفى المنصورة الدولي', 'en' => 'Mansoura International Hospital'],
                'type' => 'مستشفى',
                'governorate' => 'الدقهلية',
                'city' => 'المنصورة',
                'detailed_address' => 'شارع قناة السويس، المنصورة، الدقهلية',
                'area_search' => 'Mansoura',
                'address' => ['ar' => 'شارع قناة السويس، المنصورة، الدقهلية', 'en' => 'Suez Canal St, Mansoura, Daqahlia'],
                'phone' => '0502315000',
                'contacts' => [
                    ['name' => 'د. رامي الباز', 'phone' => '01033445566', 'job_title' => 'مدير الهندسة الطبية'],
                ]
            ],
            [
                'name' => ['ar' => 'مستشفى طنطا التخصصي للأورام', 'en' => 'Tanta Oncology Specialized Hospital'],
                'type' => 'مستشفى',
                'governorate' => 'الغربية',
                'city' => 'طنطا',
                'detailed_address' => 'شارع البحر، طنطا، الغربية',
                'area_search' => 'Tanta',
                'address' => ['ar' => 'شارع البحر، طنطا، الغربية', 'en' => 'El Bahr St, Tanta, Gharbia'],
                'phone' => '0403314500',
                'contacts' => [
                    ['name' => 'د. إيهاب سلامة', 'phone' => '01044556677', 'job_title' => 'رئيس قسم العلاج الإشعاعي'],
                    ['name' => 'م. عادل إبراهيم', 'phone' => '01244556677', 'job_title' => 'مهندس الصيانة الميدانية'],
                ]
            ],
            [
                'name' => ['ar' => 'مركز الأمل لرعاية حديثي الولادة', 'en' => 'Al-Amal NICU & Pediatric Center'],
                'type' => 'مركز طبي',
                'governorate' => 'القاهرة',
                'city' => 'مدينة نصر',
                'detailed_address' => '28 شارع مكرم عبيد، مدينة نصر، القاهرة',
                'area_search' => 'Nasr City',
                'address' => ['ar' => 'شارع مكرم عبيد، مدينة نصر، القاهرة', 'en' => 'Makram Ebeid St, Nasr City, Cairo'],
                'phone' => '0222734567',
                'contacts' => [
                    ['name' => 'د. دينا الشافعي', 'phone' => '01055667788', 'job_title' => 'مديرة المركز الطبي'],
                ]
            ],
            [
                'name' => ['ar' => 'مستشفى أسيوط الجامعي الجديد', 'en' => 'Assiut New University Hospital'],
                'type' => 'مستشفى',
                'governorate' => 'أسيوط',
                'city' => 'أسيوط',
                'detailed_address' => 'شارع الجامعة، أسيوط',
                'area_search' => 'Assiut Governorate',
                'address' => ['ar' => 'شارع الجامعة، أسيوط', 'en' => 'University St, Assiut'],
                'phone' => '0882411000',
                'contacts' => [
                    ['name' => 'د. تامر فاروق', 'phone' => '01066778899', 'job_title' => 'عميد المستشفيات الجامعية'],
                    ['name' => 'م. مجدي يوسف', 'phone' => '01166778899', 'job_title' => 'رئيس مهندسي الموقع'],
                ]
            ],
            [
                'name' => ['ar' => 'مستشفى بورسعيد التخصصي', 'en' => 'Port Said Specialized Hospital'],
                'type' => 'مستشفى',
                'governorate' => 'بورسعيد',
                'city' => 'بورسعيد',
                'detailed_address' => 'شارع 23 يوليو، بورسعيد',
                'area_search' => 'Port Said Governorate',
                'address' => ['ar' => 'شارع 23 يوليو، بورسعيد', 'en' => '23rd of July St, Port Said'],
                'phone' => '0663214567',
                'contacts' => [
                    ['name' => 'د. كمال النحاس', 'phone' => '01077889900', 'job_title' => 'مدير التجهيزات الفنية'],
                ]
            ],
            [
                'name' => ['ar' => 'مستشفى الشيخ زايد التخصصي', 'en' => 'Sheikh Zayed Specialized Hospital'],
                'type' => 'مستشفى',
                'governorate' => 'الجيزة',
                'city' => 'الشيخ زايد',
                'detailed_address' => 'الحي الأول، الشيخ زايد، الجيزة',
                'area_search' => 'Sheikh Zayed',
                'address' => ['ar' => 'الحي الأول، الشيخ زايد، الجيزة', 'en' => '1st District, Sheikh Zayed, Giza'],
                'phone' => '0238501234',
                'contacts' => [
                    ['name' => 'د. سامح عبد الفتاح', 'phone' => '01088990011', 'job_title' => 'مدير الشؤون الفنية والهندسية'],
                    ['name' => 'م. حسام رزق', 'phone' => '01288990011', 'job_title' => 'مهندس الصيانة والمتابعة'],
                ]
            ],
            [
                'name' => ['ar' => 'مركز الشروق لجراحة القسطرة والقلب', 'en' => 'El-Shorouk Cardiology Center'],
                'type' => 'مركز طبي',
                'governorate' => 'القاهرة',
                'city' => 'السيدة زينب',
                'detailed_address' => 'شارع القصر العيني، السيدة زينب، القاهرة',
                'area_search' => 'Cairo Governorate',
                'address' => ['ar' => 'شارع القصر العيني، السيدة زينب، القاهرة', 'en' => 'Kasr Al Ainy St, Cairo'],
                'phone' => '0223654321',
                'contacts' => [
                    ['name' => 'د. بلال حمادة', 'phone' => '01099001122', 'job_title' => 'استشاري القسطرة القلبية'],
                ]
            ],
            [
                'name' => ['ar' => 'مستشفى عين شمس التخصصي', 'en' => 'Ain Shams Specialized Hospital'],
                'type' => 'مستشفى',
                'governorate' => 'القاهرة',
                'city' => 'العباسية',
                'detailed_address' => 'شارع الخليفة المأمون، العباسية، القاهرة',
                'area_search' => 'Abbassia',
                'address' => ['ar' => 'العباسية، القاهرة', 'en' => 'Abbassia, Cairo'],
                'phone' => '0226845678',
                'contacts' => [
                    ['name' => 'د. علاء الدين فهمي', 'phone' => '01000112233', 'job_title' => 'مدير عام المستشفى'],
                    ['name' => 'م. سعيد عبد الرحمن', 'phone' => '01100112233', 'job_title' => 'رئيس مهندسي الصيانة بقطاع الأجهزة'],
                ]
            ],
        ];

        foreach ($clientsData as $item) {
            $area = Area::where('name->en', 'like', "%{$item['area_search']}%")
                ->orWhere('name->ar', 'like', "%{$item['area_search']}%")
                ->first();

            $areaId = $area ? $area->id : $fallbackArea;

            $client = Client::updateOrCreate(
                [
                    'name->ar' => $item['name']['ar'],
                ],
                [
                    'name' => $item['name'],
                    'type' => $item['type'] ?? 'مستشفى',
                    'governorate' => $item['governorate'] ?? null,
                    'city' => $item['city'] ?? null,
                    'detailed_address' => $item['detailed_address'] ?? null,
                    'area_id' => $areaId,
                    'address' => $item['address'],
                    'phone' => $item['phone'],
                ]
            );

            if (isset($item['contacts'])) {
                foreach ($item['contacts'] as $c) {
                    ClientContact::updateOrCreate(
                        [
                            'client_id' => $client->id,
                            'name' => $c['name'],
                        ],
                        [
                            'phone' => $c['phone'],
                            'job_title' => $c['job_title'],
                        ]
                    );
                }
            }
        }
    }
}
