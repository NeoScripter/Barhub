<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InfoItem extends Model
{
    /** @use HasFactory<\Database\Factories\InfoItemFactory> */
    use HasFactory;

    protected $with = ['image'];

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable')->where('type', 'image');
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function exhibition(): BelongsTo
    {
        return $this->belongsTo(Exhibition::class);
    }
}
