<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Person\PersonDeleteRequest;
use App\Http\Requests\Admin\Person\PersonIndexRequest;
use App\Http\Requests\Admin\Person\PersonStoreRequest;
use App\Http\Requests\Admin\Person\PersonUpdateRequest;
use App\Models\Exhibition;
use App\Models\Image;
use App\Models\Person;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Spatie\QueryBuilder\QueryBuilder;

final class PersonController extends Controller
{
    public function index(PersonIndexRequest $request)
    {
        $exhibition = Auth::user()->getActiveExhibition();

        $eventIds = $exhibition->events()->pluck('id');

        /** @var LengthAwarePaginator<Person> $people */
        $people = QueryBuilder::for(
            $exhibition->people()
        )
            ->select('people.*')
            ->withCount('events')
            ->allowedSorts(['name'])
            ->withSearch('name', $request->string('search'))
            ->paginate()
            ->through(fn($person) => tap($person, function ($p) use ($eventIds): void {
                $p->roles = $p->roles($eventIds->toArray());
            }))
            ->appends($request->query());

        return Inertia::render('admin/People/Index', [
            'people' => $people,
        ]);
    }

    public function create()
    {
        return Inertia::render('admin/People/Create');
    }

    public function store(PersonStoreRequest $request)
    {
        DB::transaction(function () use ($request) {
            $person = Person::query()->create($request->only([
                'name',
                'regalia',
                'bio',
                'telegram',
            ]));

            $exhibition = Auth::user()->getActiveExhibition();
            $exhibition->people()->syncWithoutDetaching($person->id);

            // Handle avatar
            if ($request->hasFile('avatar')) {
                Image::attachToModel(
                    $person,
                    $request->file('avatar'),
                    'avatar',
                    'people/avatars',
                    800,
                    $person->name,
                );
            }

            // Handle logo
            if ($request->hasFile('logo')) {
                Image::attachToModel(
                    $person,
                    $request->file('logo'),
                    'logo',
                    'people/logos',
                    400,
                    $person->name
                );
            }

            return $person;
        });

        return to_route('admin.people.index')
            ->with('success', 'Участник успешно создан');
    }

    public function edit(Person $person)
    {
        return Inertia::render('admin/People/Edit', [
            'person' => $person,
        ]);
    }

    public function update(PersonUpdateRequest $request, Person $person)
    {
        DB::transaction(function () use ($request, $person): void {
            // Update basic fields
            $person->update($request->only([
                'name',
                'regalia',
                'bio',
                'telegram',
            ]));

            // Handle avatar
            if ($request->hasFile('avatar')) {
                if ($person->avatar) {
                    $person->avatar->updateImage(
                        $request->file('avatar'),
                        $person->name,
                        'people/avatars',
                        200
                    );
                } else {
                    Image::attachToModel(
                        $person,
                        $request->file('avatar'),
                        'avatar',
                        'people/avatars',
                        200,
                        $person->name,
                    );
                }
            }

            // Handle logo
            if ($request->hasFile('logo')) {
                if ($person->logo) {
                    $person->logo->updateImage(
                        $request->file('logo'),
                        $person->name,
                        'people/logos',
                        400
                    );
                } else {
                    Image::attachToModel(
                        $person,
                        $request->file('logo'),
                        'logo',
                        'people/logos',
                        400,
                        $person->name,
                    );
                }
            }
        });

        return to_route('admin.people.index')
            ->with('success', 'Участник успешно обновлен');
    }

    public function destroy(PersonDeleteRequest $request, Person $person)
    {
        $person->avatar?->delete();
        $person->logo?->delete();
        $person->delete();

        return to_route('admin.people.index')
            ->with('success', 'Участник успешно удален');
    }
}
