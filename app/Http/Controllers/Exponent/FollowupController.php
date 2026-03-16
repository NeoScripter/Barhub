<?php

declare(strict_types=1);

namespace App\Http\Controllers\Exponent;

use App\Enums\FollowupStatus;
use App\Http\Controllers\Controller;
use App\Models\Followup;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

final class FollowupController extends Controller
{
    public function index()
    {
        $company = Auth::user()->company();

        abort_unless($company, 404, 'Компания не найдена');

        $followups = $company->followups()->select(['followups.comment', 'followups.status', 'followups.id'])
            ->with('service')
            ->where('status', '!=', FollowupStatus::COMPLETED)
            ->through(fn($followup): array => [
                ...$followup->toArray(),
                'status' => $followup->status->label(),
            ]);

        return Inertia::render('exponent/Followups/Index', [
            'followups' => $followups,
        ]);
    }

    public function store()
    {
        abort_if($followup->status !== FollowupStatus::IMCOMPLETE, 403);
        $followup->update(['status' => FollowupStatus::COMPLETED]);

        return to_route('exponent.followups.index' );
    }
}
