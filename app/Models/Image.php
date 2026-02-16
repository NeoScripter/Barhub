<?php

namespace App\Models;

use App\Traits\ManagesImageFiles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Image extends Model
{
    /** @use HasFactory<\Database\Factories\ImageFactory> */
    use HasFactory;
    use ManagesImageFiles;

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}
