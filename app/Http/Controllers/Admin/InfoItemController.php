<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InfoItem\InfoItemStoreRequest;
use App\Http\Requests\Admin\InfoItem\InfoItemUpdateRequest;
use App\Models\Exhibition;
use App\Models\InfoItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

final class InfoItemController extends Controller
{
    public function index(Exhibition $exhibition)
    {
        $infoItems = $exhibition->infoItems()->select(['title', 'id'])
            ->paginate();

        return Inertia::render('admin/InfoItems/Index', [
            'exhibition' => $exhibition,
            'infoItems' => $infoItems,
        ]);
    }

    public function edit(Exhibition $exhibition, InfoItem $infoItem)
    {
        return Inertia::render('admin/InfoItems/Edit', [
            'exhibition' => $exhibition,
            'infoItem' => $infoItem,
        ]);
    }

    public function create(Exhibition $exhibition)
    {
        return Inertia::render('admin/InfoItems/Create', [
            'exhibition' => $exhibition,
        ]);
    }

    public function store(InfoItemStoreRequest $request, Exhibition $exhibition)
    {
        $infoItem = $exhibition->infoItems()->create(
            $request->only(['title', 'url']),
        );

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('info-item-images');
            $infoItem->image()->create(['url' => $path]);
        }

        return to_route('admin.exhibitions.info-items.index', [
            'exhibition' => $exhibition,
        ]);
    }

    public function update(InfoItemUpdateRequest $request, Exhibition $exhibition, InfoItem $infoItem)
    {
        $infoItem->update($request->only(['title', 'url']));

        if ($request->hasFile('image')) {
            if ($infoItem->image) {
                Storage::delete($infoItem->image->url);
                $infoItem->image->delete();
            }
            $path = $request->file('image')->store('info-item-images');
            $infoItem->image()->create(['url' => $path]);
        }

        return to_route('admin.exhibitions.info-items.index', [
            'exhibition' => $exhibition,
        ]);
    }

    public function destroy(Exhibition $exhibition, InfoItem $infoItem)
    {
        $infoItem->delete();

        return to_route('admin.exhibitions.info-items.index', [
            'exhibition' => $exhibition,
        ]);
    }
}
