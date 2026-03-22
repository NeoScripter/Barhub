<?php

declare(strict_types=1);

namespace App\Http\Controllers\Exponent;

use App\Enums\FollowupStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Exponent\Followup\FollowupStoreRequest;
use App\Models\Followup;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

final class FollowupController extends Controller
{
    public function index()
    {
        $company = Auth::user()->company;

        abort_unless($company, 404, 'Компания не найдена');

        $services = $company
            ->exhibition
            ->services()
            ->get();

        $followups = $company
            ->followups
            ->map(fn($followup) => [
                ...$followup->toArray(),
                'status' => $followup->status->label(),
            ]);


        return Inertia::render('exponent/Followups/Index', [
            'followups' => $followups,
            'services' => $services,
            'company' => $company,
        ]);
    }

    public function store(FollowupStoreRequest $request)
    {
        $company = Auth::user()->company;

        abort_unless($company, 404, 'Компания не найдена');

        $validated = $request->validated();
        $service = Service::find($validated['service_id']);

        Followup::create([
            'name' => $service->name,
            'description' => $service->description,
            'comment' => $validated['comment'],
            'status' => FollowupStatus::INCOMPLETE->value,
            'user_id' => $request->user()->id,
            'company_id' => $company->id,
        ]);

        return to_route('exponent.followups.index');
    }
}
