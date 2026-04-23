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
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

final class InfoItemController extends Controller
{
    public function index()
    {
        $exhibition = Auth::user()->getActiveExhibition();
        if (!$exhibition) {
            return redirect()->route('admin.dashboard.index');
        }
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
        $exhibition = Auth::user()->getActiveExhibition();

        if (!$exhibition) {
            return redirect()->route('admin.dashboard.index');
        }

        $infoItem = $exhibition->infoItems()->create(
            $request->only(['title', 'description', 'file_name', 'url']),
        );

        if ($request->hasFile('file_url')) {
            $path = $request->file('file_url')->store('info-items-files', 'public');
            $infoItem->update([
                'file_url' => $path,
            ]);
        }

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

        return to_route('admin.info-items.index');
    }

    public function update(InfoItemUpdateRequest $request,  InfoItem $infoItem)
    {

        DB::transaction(function () use ($request, $infoItem): void {
            $infoItem->update($request->only(['title', 'description',  'url', 'file_name']));

            if ($request->hasFile('file_url')) {
                if ($infoItem->file_url) {
                    Storage::delete($infoItem->file_url);
                }
                $path = $request->file('file_url')->store('info-items-files', 'public');
                $infoItem->update(['file_url' => $path]);
            }

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
