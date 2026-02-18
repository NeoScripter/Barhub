<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PersonRole;
use App\Traits\HasFilterSearch;
use Database\Factories\PersonFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
final class Person extends Model
{
    /** @use HasFactory<PersonFactory> */
    use HasFactory;
    use HasFilterSearch;

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

    public function roles(?array $eventIds = null)
    {
        $query = DB::table('event_person')
            ->where('person_id', $this->id)
            ->distinct();

        if ($eventIds !== null) {
            $query->whereIn('event_id', $eventIds);
        }

        return $query->pluck('role')
            ->map(fn($role) => PersonRole::from($role)->label())
            ->values();
    }
}
