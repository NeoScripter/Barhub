<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Exhibition\ExhibitionUpdatedStatusRequest;
use App\Models\Exhibition;
use Illuminate\Http\RedirectResponse;

final class ExhibitionUpdatedStatusController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(ExhibitionUpdatedStatusRequest $request, Exhibition $exhibition): RedirectResponse
    {
        $exhibition->update($request->validated());

        return back();
    }
}
