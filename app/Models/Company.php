<?php

namespace App\Models;

use App\Traits\HasFilterSearch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use HasFactory;
    use HasFilterSearch;

    protected $with = ['logo'];

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function logo()
    {
        return $this->morphOne(Image::class, 'imageable')->where('type', 'logo');
    }

    public function exhibition(): BelongsTo
    {
        return $this->belongsTo(Exhibition::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
