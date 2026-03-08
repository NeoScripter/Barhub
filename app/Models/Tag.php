<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class Tag extends Model
{
    /** @use HasFactory<TagFactory> */
    use HasFactory;

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class);
    }
}
