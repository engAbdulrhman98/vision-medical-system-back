<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brandsData = [
            [
                'name' => ['ar' => 'أومرون (Omron)', 'en' => 'Omron'],
                'slug' => 'omron',
                'image' => '/images/brands/omron_logo.png',
            ],
            [
                'name' => ['ar' => 'بيورير (Beurer)', 'en' => 'Beurer'],
                'slug' => 'beurer',
                'image' => 'https://images.unsplash.com/photo-1530026405186-ed1ea0ac7a63?auto=format&fit=crop&w=200&q=80',
            ],
            [
                'name' => ['ar' => 'فيليبس ميديكال (Philips)', 'en' => 'Philips Medical'],
                'slug' => 'philips-medical',
                'image' => 'https://images.unsplash.com/photo-1516549655169-df83a0774514?auto=format&fit=crop&w=200&q=80',
            ],
            [
                'name' => ['ar' => 'أبوت (Abbott)', 'en' => 'Abbott'],
                'slug' => 'abbott',
                'image' => 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&w=300&q=80',
            ],
            [
                'name' => ['ar' => 'روش (Roche)', 'en' => 'Roche'],
                'slug' => 'roche',
                'image' => 'https://images.unsplash.com/photo-1579684389782-64d84b5e901a?auto=format&fit=crop&w=300&q=80',
            ],
            [
                'name' => ['ar' => 'جونسون آند جونسون (J&J)', 'en' => 'Johnson & Johnson'],
                'slug' => 'johnson-johnson',
                'image' => 'https://images.unsplash.com/photo-1628771065518-0d82f1113871?auto=format&fit=crop&w=300&q=80',
            ],
            [
                'name' => ['ar' => 'ليتمن (3M Littmann)', 'en' => '3M Littmann'],
                'slug' => '3m-littmann',
                'image' => 'https://images.unsplash.com/photo-1584515901387-aee001d9f56a?auto=format&fit=crop&w=300&q=80',
            ],
            [
                'name' => ['ar' => 'زيمر بايمت (Zimmer Biomet)', 'en' => 'Zimmer Biomet'],
                'slug' => 'zimmer-biomet',
                'image' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?auto=format&fit=crop&w=300&q=80',
            ],
            [
                'name' => ['ar' => 'سترايكر (Stryker)', 'en' => 'Stryker'],
                'slug' => 'stryker',
                'image' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=300&q=80',
            ],
            [
                'name' => ['ar' => 'فاميد (FAMED)', 'en' => 'FAMED'],
                'slug' => 'famed',
                'image' => 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?auto=format&fit=crop&w=300&q=80',
            ],
            [
                'name' => ['ar' => 'ترامف (TRUMPF)', 'en' => 'TRUMPF'],
                'slug' => 'trumpf',
                'image' => 'https://images.unsplash.com/photo-1530490125741-9d5d40afb5f1?auto=format&fit=crop&w=300&q=80',
            ]
        ];

        foreach ($brandsData as $brnd) {
            $brand = Brand::where('slug', $brnd['slug'])->first();
            if (!$brand) {
                $brand = new Brand();
                $brand->slug = $brnd['slug'];
            }
            $brand->setTranslation('name', 'ar', $brnd['name']['ar']);
            $brand->setTranslation('name', 'en', $brnd['name']['en']);
            $brand->save();

            if ($brand->getFirstMediaUrl('logo') === '' && !empty($brnd['image'])) {
                try {
                    if (str_starts_with($brnd['image'], 'http')) {
                        $brand->addMediaFromUrl($brnd['image'])->toMediaCollection('logo');
                    } else {
                        // Local public file path
                        $localPath = public_path($brnd['image']);
                        if (file_exists($localPath)) {
                            $brand->addMedia($localPath)
                                  ->preservingOriginal()
                                  ->toMediaCollection('logo');
                        }
                    }
                } catch (\Exception $e) {
                    // Fail silently
                }
            }
        }
    }
}
