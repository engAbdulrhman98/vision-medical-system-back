<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class Product extends Model
{
    use HasTranslations;

    protected static function booted()
    {
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $name = $product->name;
                $nameEn = 'product';
                if (is_array($name)) {
                    $nameEn = $name['en'] ?? $name['ar'] ?? 'product';
                } elseif (is_string($name)) {
                    $decoded = json_decode($name, true);
                    if (is_array($decoded)) {
                        $nameEn = $decoded['en'] ?? $decoded['ar'] ?? 'product';
                    } else {
                        $nameEn = $name;
                    }
                }
                $product->slug = \Illuminate\Support\Str::slug($nameEn) . '-' . rand(1000, 9999);
            }
        });
    }

    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'slug',
        'sku',
        'description',
        'details',
        'price',
        'image',
        'in_stock',
    ];

    protected $casts = [
        'in_stock' => 'boolean',
        'price' => 'decimal:2',
    ];

    public array $translatable = ['name', 'description', 'details'];

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the brand that owns the product.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the reviews for the product.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the approved reviews for the product.
     */
    public function approvedReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    /**
     * Calculate average rating.
     */
    public function averageRating(): float
    {
        return round($this->approvedReviews()->avg('rating') ?? 5.0, 1);
    }

    /**
     * Check if the product media is a video file.
     */
    public function isVideo(): bool
    {
        if (empty($this->image)) {
            return false;
        }
        $extension = strtolower(pathinfo($this->image, PATHINFO_EXTENSION));
        return in_array($extension, ['mp4', 'webm', 'ogg', 'mov', 'avi']);
    }

    /**
     * Get the devices associated with the product.
     */
    public function devices(): HasMany
    {
        return $this->hasMany(Device::class, 'product_id');
    }

    /**
     * Get the stock item record for the product.
     */
    public function stockItem(): HasOne
    {
        return $this->hasOne(StockItem::class, 'product_id');
    }
}
