<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Exhibition\ExhibitionUpdateStatusRequest;
use App\Models\Exhibition;

final class UpdatedExhibitionStatusController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(ExhibitionUpdateStatusRequest $request, Exhibition $exhibition): RedirectResponse
    {
        $exhibition->update($request->validated());

        return back();
    }
}
