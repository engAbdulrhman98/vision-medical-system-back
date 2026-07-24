<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name',
        'type',
        'parent_id',
    ];

    public array $translatable = ['name'];

    /**
     * Convert the model instance to an array with flat translated string fields.
     */
    public function toArray()
    {
        $attributes = parent::toArray();
        $locale = app()->getLocale() ?: 'ar';

        foreach ($this->translatable as $field) {
            if (isset($attributes[$field])) {
                $attributes[$field] = $this->getTranslation($field, $locale) 
                    ?: ($this->getTranslation($field, 'ar') ?: $attributes[$field]);
            }
        }

        return $attributes;
    }

    /**
     * Get the parent area.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'parent_id');
    }

    /**
     * Get the child areas.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Area::class, 'parent_id');
    }

    /**
     * Get the clients located in this area.
     */
    public function clients(): HasMany
    {
        return $this->hasMany(Client::class, 'area_id');
    }

    /**
     * Scope a query to only include areas of a given type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
