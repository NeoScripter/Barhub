<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\FollowupStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Followup\FollowupIndexRequest;
use App\Models\Followup;
use App\Sorts\RelationSort;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

final class FollowupController extends Controller
{
    public function index(FollowupIndexRequest $request)
    {
        $exhibition = Auth::user()->getActiveExhibition();

        $followups = QueryBuilder::for(Followup::query()->select(['followups.comment', 'followups.status', 'followups.id'])
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
            'followups' => $followups,
        ]);
    }

    public function edit(Followup $followup)
    {
        $followup->load(['service.company:public_name,id', 'user:name,id']);

        return Inertia::render('admin/Followups/Edit', [
            'followup' => $followup,
        ]);
    }

    public function update(Followup $followup)
    {
        abort_if($followup->status !== FollowupStatus::INCOMPLETE, 403);
        $followup->update(['status' => FollowupStatus::COMPLETED]);

        return to_route('admin.followups.index' );
    }
}
