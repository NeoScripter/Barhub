<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Tag\TagDestroyRequest;
use App\Http\Requests\Admin\Tag\TagStoreRequest;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;

final class TagController extends Controller
{
    public function store(TagStoreRequest $request): RedirectResponse
    {
        Tag::query()->create($request->validated());

        return back();
    }

    public function destroy(TagDestroyRequest $request, Tag $tag): RedirectResponse
    {
        $tag->delete();

        return back();
    }
}
