<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PersonRole;
use Database\Factories\PersonFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class Person extends Model
{
    /** @use HasFactory<PersonFactory> */
    use HasFactory;

    protected $with = ['avatar', 'logo'];

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class)
            ->withPivot('role');
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
