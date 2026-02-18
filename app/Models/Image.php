<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\ManagesImageFiles;
use Database\Factories\ImageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class Image extends Model
{
    /** @use HasFactory<ImageFactory> */
    use HasFactory;

    use ManagesImageFiles;

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}
