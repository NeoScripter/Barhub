<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\FollowupStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Followup\FollowupIndexRequest;
use App\Http\Requests\Admin\Followup\FollowupUpdateRequest;
use App\Models\Exhibition;
use App\Models\Followup;
use App\Sorts\RelationSort;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

final class FollowupController extends Controller
{
    public function index(FollowupIndexRequest $request, Exhibition $exhibition)
    {
        $followups = QueryBuilder::for(Followup::select(['followup.comment', 'followup.status', 'followup.id'])
            ->forExhibition($exhibition->id))
            ->with('service')
            ->where('status', '!=', FollowupStatus::COMPLETED)
            ->allowedSorts([
                AllowedSort::custom('service.name', new RelationSort('services', 'name', 'service_id')),
            ])
            ->paginate()
            ->through(fn($followup): array => [
                ...$followup->toArray(),
                'status' => $followup->status->label(),
            ])
            ->appends($request->query());

        return Inertia::render('admin/Followups/Index', [
            'exhibition' => $exhibition,
            'followups' => $followups,
        ]);
    }

    public function edit(Exhibition $exhibition, Followup $followup)
    {
        $followup = $followup;
        $followup->load(['service']);

        return Inertia::render('admin/Followups/Edit', [
            'exhibition' => $exhibition,
            'followup' => $followup,
        ]);
    }

    public function update(FollowupUpdateRequest $request, Exhibition $exhibition, Followup $followup)
    {
        if ($followup->status !== FollowupStatus::IMCOMPLETE) {
            abort(403);
        }
        $followup = $followup;
        $newStatus = $request->boolean('is_accepted') === true ? FollowupStatus::COMPLETED : FollowupStatus::IMCOMPLETE;
        $followup->update(['status' => $newStatus]);

        return to_route('admin.exhibitions.all-followups.index', [
            'exhibition' => $exhibition
        ]);
    }
}
