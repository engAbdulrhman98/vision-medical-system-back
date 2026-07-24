<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoriesData = [
            [
                'name' => ['ar' => 'الأجهزة التشخيصية', 'en' => 'Diagnostic Devices'],
                'slug' => 'diagnostic-devices',
                'description' => ['ar' => 'أجهزة قياس الضغط، السكر، الحرارة وأجهزة التشخيص الدقيقة.', 'en' => 'Blood pressure, glucose, temperature monitors and precision diagnostic tools.'],
                'image' => 'https://images.unsplash.com/photo-1516549655169-df83a0774514?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => ['ar' => 'المستلزمات الطبية', 'en' => 'Medical Supplies'],
                'slug' => 'medical-supplies',
                'description' => ['ar' => 'كمامات، قفازات، معقمات وأدوات الرعاية اليومية الطبية.', 'en' => 'Masks, gloves, sanitizers and daily medical care tools.'],
                'image' => 'https://images.unsplash.com/photo-1583324113626-70df0f4decab?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => ['ar' => 'العيون والبصريات', 'en' => 'Ophthalmology & Optics'],
                'slug' => 'ophthalmology-optics',
                'description' => ['ar' => 'عدسات، قطرات عيون وأجهزة فحص النظر المتطورة.', 'en' => 'Lenses, eye drops and advanced vision exam equipment.'],
                'image' => 'https://images.unsplash.com/photo-1508962914676-134849a727f0?auto=format&fit=crop&w=800&q=80',
            ]
        ];

        foreach ($categoriesData as $cat) {
            $category = Category::where('slug', $cat['slug'])->first();
            if (!$category) {
                $category = new Category();
                $category->slug = $cat['slug'];
            }
            $category->image = $cat['image'];
            $category->setTranslation('name', 'ar', $cat['name']['ar']);
            $category->setTranslation('name', 'en', $cat['name']['en']);
            $category->setTranslation('description', 'ar', $cat['description']['ar']);
            $category->setTranslation('description', 'en', $cat['description']['en']);
            $category->save();
        }
    }
}
