<?php

declare(strict_types=1);

namespace App\Models;

use App\Jobs\Integration\SyncThemeJob;
use Database\Factories\ThemeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class Theme extends Model
{
    /** @use HasFactory<ThemeFactory> */
    use HasFactory;

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class);
    }

    public function exhibition(): BelongsTo
    {
        return $this->belongsTo(Exhibition::class);
    }

    protected static function booted()
    {
        static::created(function (Theme $theme) {
            SyncThemeJob::dispatch($theme->id, 'create');
        });

        static::updated(function (Theme $theme) {
            SyncThemeJob::dispatch($theme->id, 'update');
        });

        static::deleted(function (Theme $theme) {
            SyncThemeJob::dispatch($theme->id, 'delete');
        });
    }
}
