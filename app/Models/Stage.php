<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\StageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Stage extends Model
{
    /** @use HasFactory<StageFactory> */
    use HasFactory;

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
