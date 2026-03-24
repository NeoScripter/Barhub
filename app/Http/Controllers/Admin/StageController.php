<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Stage\StageDestroyRequest;
use App\Http\Requests\Admin\Stage\StageStoreRequest;
use App\Models\Stage;
use Illuminate\Http\RedirectResponse;

final class StageController extends Controller
{
    public function store(StageStoreRequest $request): RedirectResponse
    {
        $exhibition = $request->user()->getActiveExhibition();
        Stage::query()->create(
            [...$request->validated(), 'exhibition_id' => $exhibition->id]
        );

        return back()
            ->with('success', 'Площадка успешно создана');
    }

    public function destroy(StageDestroyRequest $request, Stage $stage): RedirectResponse
    {
        $stage->delete();

        return back()
            ->with('success', 'Площадка успешно удалена');
    }
}
