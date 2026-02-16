<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PersonRole;
use Database\Factories\PersonFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Person extends Model
{
    /** @use HasFactory<PersonFactory> */
    use HasFactory;

    protected $with = ['roleAssignments', 'avatar', 'logo'];

    protected $appends = ['roles'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function roleAssignments(): HasMany
    {
        return $this->hasMany(PersonRoleAssignment::class);
    }

    protected function roles(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->roleAssignments
                ->pluck('role')
                ->map(fn(PersonRole $role) => $role->label())
                ->all()
        );
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function avatar()
    {
        return $this->morphOne(Image::class, 'imageable')->where('type', 'avatar');
    }

    public function logo()
    {
        return $this->morphOne(Image::class, 'imageable')->where('type', 'logo');
    }
}
