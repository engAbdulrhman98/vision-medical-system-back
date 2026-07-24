<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Category extends Model
{
    use HasFactory, HasTranslations;

    protected static function booted()
    {
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $name = $category->name;
                $nameEn = 'category';
                if (is_array($name)) {
                    $nameEn = $name['en'] ?? $name['ar'] ?? 'category';
                } elseif (is_string($name)) {
                    $decoded = json_decode($name, true);
                    if (is_array($decoded)) {
                        $nameEn = $decoded['en'] ?? $decoded['ar'] ?? 'category';
                    } else {
                        $nameEn = $name;
                    }
                }
                $category->slug = \Illuminate\Support\Str::slug($nameEn) . '-' . rand(1000, 9999);
            }
        });
    }

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
    ];

    public array $translatable = ['name', 'description'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
