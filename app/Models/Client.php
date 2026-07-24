<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Client extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name',
        'type',
        'area_id',
        'governorate',
        'city',
        'detailed_address',
        'notes',
        'address',
        'phone',
    ];

    public array $translatable = ['name', 'address'];

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(ClientContact::class);
    }

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        $attributes = parent::toArray();

        if (isset($attributes['name']) && is_array($attributes['name'])) {
            $locale = app()->getLocale();
            $attributes['name'] = $attributes['name'][$locale] 
                ?? $attributes['name']['ar'] 
                ?? $attributes['name']['en'] 
                ?? (reset($attributes['name']) ?: '');
        }

        if (isset($attributes['address']) && is_array($attributes['address'])) {
            $locale = app()->getLocale();
            $attributes['address'] = $attributes['address'][$locale] 
                ?? $attributes['address']['ar'] 
                ?? $attributes['address']['en'] 
                ?? (reset($attributes['address']) ?: '');
        }

        return $attributes;
    }
}
