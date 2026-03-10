<?php

namespace App\Models;

use App\Enums\FollowupStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Followup extends Model
{
    /** @use HasFactory<\Database\Factories\FollowupFactory> */
    use HasFactory;

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
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

    public function scopeForExhibition(Builder $query, int $exhibitionId): Builder
    {
        return $query
            ->join('services', "followups.service_id", '=', 'services.id')
            ->join('companies', 'services.company_id', '=', 'companies.id')
            ->where('companies.exhibition_id', $exhibitionId)
            ->select("followups.*");
    }

    public function scopeForCompany(Builder $query, int $companyId): Builder
    {
        return $query
            ->join('services', "followups.service_id", '=', 'services.id')
            ->where('services.company_id', $companyId)
            ->select("followups.*");
    }
}
