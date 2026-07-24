<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $guarded = [];
    protected $fillable = ['product_id', 'user_name', 'reviewer_name', 'rating', 'comment', 'approved'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
