<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Brand extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia, HasSlug;

    protected $fillable = [
        'name',
    ];

    public array $translatable = ['name'];

    protected $appends = ['image_url', 'thumb_url'];

    public function products(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(function ($model) {
                $name = $model->name;
                if (is_array($name)) {
                    return $name['en'] ?? $name['ar'] ?? '';
                }
                return $model->getTranslation('name', 'en') ?: $model->getTranslation('name', 'ar') ?: '';
            })
            ->saveSlugsTo('slug');
    }

    /**
     * Get the brand's logo image URL.
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getFirstMediaUrl('logo') ?: null,
        );
    }

    /**
     * Get the brand's logo thumbnail URL.
     */
    protected function thumbUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getFirstMediaUrl('logo', 'thumb') ?: null,
        );
    }

    /**
     * Register the media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')
            ->singleFile();
    }

    /**
     * Register the media conversions.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(100)
            ->height(100)
            ->nonQueued();
    }
}
