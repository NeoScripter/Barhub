<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Exhibition\ExhibitionUpdateStatusRequest;
use App\Models\Exhibition;

class UpdatedExhibitionStatusController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(ExhibitionUpdateStatusRequest $request, Exhibition $exhibition)
    {
        $exhibition->update($request->validated());

        return redirect()->back();
    }
}
