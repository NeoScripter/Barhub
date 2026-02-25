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
        Stage::create($request->validated());

        return redirect()
            ->back()
            ->with('success', 'Площадка успешно создана');
    }

    public function destroy(StageDestroyRequest $request, Stage $stage): RedirectResponse
    {
        $stage->delete();

        return redirect()
            ->back()
            ->with('success', 'Площадка успешно удалена');
    }
}
