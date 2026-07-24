<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BrandApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /**
     * Test listing brands.
     */
    public function test_anyone_can_list_brands()
    {
        Brand::create([
            'name' => [
                'ar' => 'سامسونج',
                'en' => 'Samsung',
            ],
        ]);

        $response = $this->getJson('/api/brands');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'ar' => 'سامسونج',
                'en' => 'Samsung',
            ]);
    }

    /**
     * Test filtering brands using Spatie QueryBuilder.
     */
    public function test_anyone_can_filter_brands_by_slug()
    {
        $brand1 = Brand::create([
            'name' => [
                'ar' => 'سامسونج',
                'en' => 'Samsung',
            ],
        ]);

        $brand2 = Brand::create([
            'name' => [
                'ar' => 'أبل',
                'en' => 'Apple',
            ],
        ]);

        $response = $this->getJson('/api/brands?filter[slug]=' . $brand1->slug);

        $response->assertStatus(200)
            ->assertJsonFragment(['slug' => $brand1->slug])
            ->assertJsonMissing(['slug' => $brand2->slug]);
    }

    /**
     * Test searching brands.
     */
    public function test_anyone_can_search_brands()
    {
        $brand1 = Brand::create([
            'name' => [
                'ar' => 'سامسونج تلفونات',
                'en' => 'Samsung Phones',
            ],
        ]);

        $brand2 = Brand::create([
            'name' => [
                'ar' => 'أبل ايباد',
                'en' => 'Apple iPad',
            ],
        ]);

        // Search by English name keyword
        $responseEn = $this->getJson('/api/brands?filter[search]=Phones');
        $responseEn->assertStatus(200)
            ->assertJsonFragment(['slug' => $brand1->slug])
            ->assertJsonMissing(['slug' => $brand2->slug]);

        // Search by Arabic name keyword
        $responseAr = $this->getJson('/api/brands?filter[search]=ايباد');
        $responseAr->assertStatus(200)
            ->assertJsonFragment(['slug' => $brand2->slug])
            ->assertJsonMissing(['slug' => $brand1->slug]);
    }

    /**
     * Test retrieving a specific brand by its slug (default route key).
     */
    public function test_anyone_can_show_brand_by_slug()
    {
        $brand = Brand::create([
            'name' => [
                'ar' => 'أبل',
                'en' => 'Apple',
            ],
        ]);

        $response = $this->getJson('/api/brands/' . $brand->slug);

        $response->assertStatus(200)
            ->assertJsonPath('data.name.en', 'Apple')
            ->assertJsonPath('data.name.ar', 'أبل')
            ->assertJsonPath('data.slug', $brand->slug);
    }

    /**
     * Test guests cannot create brands.
     */
    public function test_guest_cannot_create_brand()
    {
        $response = $this->postJson('/api/brands', [
            'name' => [
                'ar' => 'توشيبا',
                'en' => 'Toshiba',
            ],
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test authenticated users can create brands with media library.
     */
    public function test_authenticated_user_can_create_brand()
    {
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);

        $file = UploadedFile::fake()->image('toshiba.jpg');

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/brands', [
                'name' => [
                    'ar' => 'توشيبا',
                    'en' => 'Toshiba',
                ],
                'image' => $file,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('brand.name.ar', 'توشيبا')
            ->assertJsonPath('brand.name.en', 'Toshiba')
            ->assertJsonPath('brand.slug', 'toshiba');

        $brand = Brand::first();
        $this->assertCount(1, $brand->getMedia('logo'));
        
        $media = $brand->getFirstMedia('logo');
        $this->assertTrue(Storage::disk('public')->exists($media->getPathRelativeToRoot()));
        $this->assertNotNull($response->json('brand.image_url'));
        $this->assertNotNull($response->json('brand.thumb_url'));
    }

    /**
     * Test brand creation validation.
     */
    public function test_brand_creation_validation()
    {
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/brands', [
                'name' => [
                    'ar' => '',
                ],
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name.ar', 'name.en', 'image']);
    }

    /**
     * Test brand update replaces old media.
     */
    public function test_authenticated_user_can_update_brand()
    {
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);

        $brand = Brand::create([
            'name' => [
                'ar' => 'ال جي',
                'en' => 'LG',
            ],
        ]);

        // Add initial media
        $file1 = UploadedFile::fake()->image('lg.jpg');
        $brand->addMedia($file1)->toMediaCollection('logo');
        
        $oldMedia = $brand->getFirstMedia('logo');
        $this->assertTrue(Storage::disk('public')->exists($oldMedia->getPathRelativeToRoot()));

        // Update with new name and new file
        $file2 = UploadedFile::fake()->image('lg_new.jpg');

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/brands/' . $brand->slug, [
                'name' => [
                    'ar' => 'ال جي المعدلة',
                    'en' => 'LG Updated',
                ],
                'image' => $file2,
            ]);

        $response->assertStatus(200);

        $brand->refresh();
        $this->assertEquals('LG Updated', $brand->getTranslation('name', 'en'));
        $this->assertEquals('ال جي المعدلة', $brand->getTranslation('name', 'ar'));
        $this->assertEquals('lg-updated', $brand->slug); // Sluggable auto-updates slug if generated field changes!

        // Verify old media is deleted from storage and new exists
        $newMedia = $brand->getFirstMedia('logo');
        $this->assertFalse(Storage::disk('public')->exists($oldMedia->getPathRelativeToRoot()));
        $this->assertTrue(Storage::disk('public')->exists($newMedia->getPathRelativeToRoot()));
    }

    /**
     * Test brand deletion removes media.
     */
    public function test_authenticated_user_can_delete_brand()
    {
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);

        $brand = Brand::create([
            'name' => [
                'ar' => 'ال جي',
                'en' => 'LG',
            ],
        ]);

        $file = UploadedFile::fake()->image('lg.jpg');
        $brand->addMedia($file)->toMediaCollection('logo');
        $media = $brand->getFirstMedia('logo');

        $this->assertTrue(Storage::disk('public')->exists($media->getPathRelativeToRoot()));

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson('/api/brands/' . $brand->slug);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('brands', ['id' => $brand->id]);
        $this->assertFalse(Storage::disk('public')->exists($media->getPathRelativeToRoot()));
    }

    /**
     * Test exporting brands.
     */
    public function test_authenticated_user_can_export_brands()
    {
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);

        Brand::create([
            'name' => [
                'ar' => 'سامسونج',
                'en' => 'Samsung',
            ],
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/brands/export');

        $response->assertStatus(200)
            ->assertHeader('content-disposition', 'attachment; filename=brands.xlsx');
    }

    /**
     * Test importing brands.
     */
    public function test_authenticated_user_can_import_brands()
    {
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);

        $csvContent = "name_ar,name_en\nتوشيبا,Toshiba\nديل,Dell";
        $file = UploadedFile::fake()->createWithContent('brands.csv', $csvContent);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/brands/import', [
                'file' => $file,
            ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Brands imported successfully']);

        $this->assertDatabaseHas('brands', ['slug' => 'toshiba']);
        $this->assertDatabaseHas('brands', ['slug' => 'dell']);
    }
}
