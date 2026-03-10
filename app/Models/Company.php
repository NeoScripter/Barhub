<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasFilterSearch;
use Database\Factories\CompanyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Company extends Model
{
    /** @use HasFactory<CompanyFactory> */
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

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function followups()
    {
        return $this->hasManyThrough(
            Followup::class,
            Service::class,
            'company_id',
            'service_id'
        );
    }
}
