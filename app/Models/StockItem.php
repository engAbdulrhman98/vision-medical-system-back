<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class StockItem extends Model
{
    use LogsActivity;

    /**
     * Configure activity logging for the StockItem model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['product_id', 'quantity', 'location', 'minimum_quantity'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn(string $eventName) => "تم {$eventName} عنصر المخزون / Stock Item has been {$eventName}");
    }
    protected $fillable = [
        'product_id',
        'quantity',
        'location',
        'minimum_quantity',
    ];

    /**
     * Get the product this stock item represents.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
