<?php

declare(strict_types=1);

namespace App\Models;

use App\Events\ExhibitionCreated;
use Database\Factories\ExhibitionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Exhibition extends Model
{
    /** @use HasFactory<ExhibitionFactory> */
    use HasFactory;

    protected $dispatchesEvents = [
        'created' => ExhibitionCreated::class,
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function infoItems(): HasMany
    {
        return $this->hasMany(InfoItem::class);
    }

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function taskTemplates(): HasMany
    {
        return $this->hasMany(TaskTemplate::class);
    }

    protected function casts(): array
    {
        return [
            'starts_at' => 'date',
            'ends_at' => 'date',
        ];
    }
}
