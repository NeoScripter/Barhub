<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\FollowupStatus;
use Database\Factories\FollowupFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Followup extends Model
{
    /** @use HasFactory<FollowupFactory> */
    use HasFactory;

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => FollowupStatus::class,
        ];
    }

    #[Scope]
    protected function forExhibition(Builder $query, int $exhibitionId): Builder
    {
        return $query
            ->join('companies', 'followups.company_id', '=', 'companies.id')
            ->where('companies.exhibition_id', $exhibitionId)
            ->select('followups.*');
    }
}
