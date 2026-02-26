<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Person\PersonIndexRequest;
use App\Http\Requests\Admin\Person\PersonStoreRequest;
use App\Http\Requests\Admin\Person\PersonUpdateRequest;
use App\Models\Exhibition;
use App\Models\Image;
use App\Models\Person;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Spatie\QueryBuilder\QueryBuilder;

final class PersonController extends Controller
{
    public function index(PersonIndexRequest $request, Exhibition $exhibition)
    {
        $eventIds = $exhibition->events()->pluck('id');

        /** @var LengthAwarePaginator<Person> $people */
        $people = QueryBuilder::for(
            Person::whereHas('events', fn($q) => $q->whereIn('events.id', $eventIds))
        )
            ->select('people.*')
            ->withCount('events')
            ->allowedSorts(['name'])
            ->withSearch('name', $request->string('search'))
            ->paginate()
            ->through(fn($person) => tap($person, function ($p) use ($eventIds) {
                $p->roles = $p->roles($eventIds->toArray());
            }))
            ->appends($request->query());

        return Inertia::render('admin/People/Index', [
            'exhibition' => $exhibition,
            'people' => $people,
        ]);
    }

    public function create(Exhibition $exhibition)
    {
        return Inertia::render('admin/People/Create', [
            'exhibition' => $exhibition,
        ]);
    }

    public function store(PersonStoreRequest $request, Exhibition $exhibition)
    {
        $person = DB::transaction(function () use ($request) {
            $person = Person::create($request->only([
                'name',
                'regalia',
                'bio',
                'telegram',
            ]));

            // Handle avatar
            if ($request->hasFile('avatar')) {
                Image::attachToModel(
                    $person,
                    $request->file('avatar'),
                    'avatar',
                    'people/avatars',
                    800,
                    $request->input('avatar_alt', '')
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
                    $request->input('logo_alt', '')
                );
            }

            return $person;
        });

        return redirect()
            ->route('admin.exhibitions.people.index', $exhibition)
            ->with('success', 'Участник успешно создан');
    }

    public function edit(Exhibition $exhibition, Person $person)
    {
        return Inertia::render('admin/People/Edit', [
            'exhibition' => $exhibition,
            'person' => $person,
        ]);
    }

    public function update(PersonUpdateRequest $request, Exhibition $exhibition, Person $person)
    {
        DB::transaction(function () use ($request, $person) {
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
                        $request->input('avatar_alt'),
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
                        $request->input('avatar_alt', '')
                    );
                }
            } elseif ($request->has('avatar_alt') && $person->avatar) {
                // Update only alt text
                $person->avatar->updateImage(null, $request->input('avatar_alt'));
            }

            // Handle logo
            if ($request->hasFile('logo')) {
                if ($person->logo) {
                    $person->logo->updateImage(
                        $request->file('logo'),
                        $request->input('logo_alt'),
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
                        $request->input('logo_alt', '')
                    );
                }
            } elseif ($request->has('logo_alt') && $person->logo) {
                // Update only alt text
                $person->logo->updateImage(null, $request->input('logo_alt'));
            }
        });

        return redirect()
            ->route('admin.exhibitions.people.index', $exhibition)
            ->with('success', 'Участник успешно обновлен');
    }

    public function destroy(Exhibition $exhibition, Person $person)
    {
        $person->delete();

        return redirect()
            ->route('admin.exhibitions.people.index', $exhibition)
            ->with('success', 'Участник успешно удален');
    }
}
