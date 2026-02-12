<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PersonRole;
use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class Event extends Model
{
    /** @use HasFactory<EventFactory> */
    use HasFactory;

    public function exhibition(): BelongsTo
    {
        return $this->belongsTo(Exhibition::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(Stage::class);
    }

    public function themes(): BelongsToMany
    {
        return $this->belongsToMany(Theme::class);
    }

    public function people(): HasMany
    {
        return $this->hasMany(Person::class);
    }

    public function organizer(): HasOne
    {
        return $this->hasOne(Person::class)
            ->whereHas('roleAssignments', function ($query) {
                $query->where('role', PersonRole::ORGANIZER->value);
            });
    }
}
