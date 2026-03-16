<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InfoItem\InfoItemStoreRequest;
use App\Http\Requests\Admin\InfoItem\InfoItemUpdateRequest;
use App\Models\Image;
use App\Models\InfoItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

final class InfoItemController extends Controller
{
    public function index()
    {
        $exhibition = Auth::user()->getActiveExhibition();
        $infoItems = $exhibition->infoItems()
            ->select(['title',  'id'])
            ->paginate();

        return Inertia::render('admin/InfoItems/Index', [
            'infoItems' => $infoItems,
        ]);
    }

    public function edit(InfoItem $infoItem)
    {
        return Inertia::render('admin/InfoItems/Edit', [
            'infoItem' => $infoItem,
        ]);
    }

    public function create()
    {
        return Inertia::render('admin/InfoItems/Create');
    }

    public function store(InfoItemStoreRequest $request)
    {
        DB::transaction(function () use ($request) {
            $exhibition = Auth::user()->getActiveExhibition();
            $infoItem = $exhibition->infoItems()->create(
                $request->only(['title', 'url']),
            );

            if ($request->hasFile('image')) {
                Image::attachToModel(
                    $infoItem,
                    $request->file('image'),
                    'image',
                    'info-items/images',
                    80,
                    $infoItem->title,
                );
            }
        });

        return to_route('admin.info-items.index');
    }

    public function update(InfoItemUpdateRequest $request,  InfoItem $infoItem)
    {

        DB::transaction(function () use ($request, $infoItem): void {
            $infoItem->update($request->only(['title', 'url']));

            if ($request->hasFile('image')) {
                if ($infoItem->image) {
                    $infoItem->image->updateImage(
                        $request->file('image'),
                        $infoItem->title,
                        'info-items/images',
                        80
                    );
                } else {
                    Image::attachToModel(
                        $infoItem,
                        $request->file('image'),
                        'image',
                        'info-items/images',
                        80,
                        $infoItem->title,
                    );
                }
            }
        });

        return to_route('admin.info-items.index');
    }

    public function destroy(InfoItem $infoItem)
    {
        $infoItem->image?->delete();
        $infoItem->delete();

        return to_route('admin.info-items.index');
    }
}
