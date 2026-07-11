<?php

declare(strict_types=1);

namespace App\Models;

use App\Jobs\Integration\SyncStageJob;
use Database\Factories\StageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Stage extends Model
{
    /** @use HasFactory<StageFactory> */
    use HasFactory;

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function exhibition(): BelongsTo
    {
        return $this->belongsTo(Exhibition::class);
    }

    protected static function booted()
    {
        static::created(function (Stage $stage) {
            SyncStageJob::dispatch($stage->id, 'create');
        });

        static::updated(function (Stage $stage) {
            SyncStageJob::dispatch($stage->id, 'update');
        });

        static::deleted(function (Stage $stage) {
            SyncStageJob::dispatch($stage->id, 'delete');
        });
    }
}
