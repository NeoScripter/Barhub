<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Theme\ThemeDestroyRequest;
use App\Http\Requests\Admin\Theme\ThemeStoreRequest;
use App\Models\Theme;
use Illuminate\Http\RedirectResponse;

final class ThemeController extends Controller
{
    public function store(ThemeStoreRequest $request): RedirectResponse
    {
        Theme::query()->create($request->validated());

        return back()
            ->with('success', 'Направление успешно создано');
    }

    public function destroy(ThemeDestroyRequest $request, Theme $theme): RedirectResponse
    {
        $theme->delete();

        return back()
            ->with('success', 'Направление успешно удалено');
    }
}
