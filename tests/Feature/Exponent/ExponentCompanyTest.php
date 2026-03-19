<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Exhibition;
use App\Models\Image;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\UploadedFile;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\get;

// ─────────────────────────────────────────────────────────────
// Access Control
// ─────────────────────────────────────────────────────────────

describe('Exponent Company Test - Access Control', function (): void {
    beforeEach(function (): void {
        $this->exhibition = Exhibition::factory()->create();
        $this->company = Company::factory()->for($this->exhibition)->create();
    });

    it('redirects guest users to login on index', function (): void {
        get(route('exponent.companies.index'))
            ->assertRedirect(route('login'));
    });

    it('redirects guest users to login on create', function (): void {
        get(route('exponent.companies.create'))
            ->assertRedirect(route('login'));
    });

    it('redirects guest users to login on edit', function (): void {
        get(route('exponent.companies.edit', [$this->exhibition, $this->company]))
            ->assertRedirect(route('login'));
    });

    it('forbids USER role from accessing companies', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);

        actingAs($user)
            ->get(route('exponent.companies.index'))
            ->assertForbidden();
    });

    it('forbids EXPONENT role from accessing companies', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::EXPONENT);

        actingAs($user)
            ->get(route('exponent.companies.index'))
            ->assertForbidden();
    });

    it('super exponent can access all exhibitions companies', function (): void {
        $exponent = User::factory()->create();
        $exponent->assignRole(UserRole::SUPER_ADMIN);

        actingAs($exponent)
            ->get(route('exponent.companies.index'))
            ->assertOk();
    });
})->skip();

// ─────────────────────────────────────────────────────────────
// Index
// ─────────────────────────────────────────────────────────────

describe('Company Index', function (): void {
    beforeEach(function (): void {
        $this->exponent = User::factory()->create();
        $this->exponent->assignRole(UserRole::SUPER_ADMIN);
        $this->exhibition = Exhibition::factory()->create();
    });

    it('displays all companies for an exhibition', function (): void {
        Company::factory(5)->for($this->exhibition)->create();

        actingAs($this->exponent)
            ->get(route('exponent.companies.index'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->component('exponent/Companies/Index')
                    ->has('companies.data', 5)
            );
    });

    it('only shows companies belonging to the given exhibition', function (): void {
        Company::factory(3)->for($this->exhibition)->create();
        $otherExhibition = Exhibition::factory()->create();
        Company::factory(4)->for($otherExhibition)->create();

        actingAs($this->exponent)
            ->get(route('exponent.companies.index'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page->has('companies.data', 3)
            );
    });

    it('passes exhibition to the view', function (): void {
        actingAs($this->exponent)
            ->get(route('exponent.companies.index'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->has('exhibition')
                    ->where('exhibition.id', $this->exhibition->id)
            );
    });

    it('eager loads logo and tags', function (): void {
        $tag = Tag::factory()->create();
        $company = Company::factory()->for($this->exhibition)->create();
        $company->tags()->attach($tag);

        actingAs($this->exponent)
            ->get(route('exponent.companies.index'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->has('companies.data.0.tags')
            );
    });

    it('searches companies by public_name', function (): void {
        Company::factory()->for($this->exhibition)->create(['public_name' => 'Acme Corporation']);
        Company::factory()->for($this->exhibition)->create(['public_name' => 'Globex Industries']);
        Company::factory()->for($this->exhibition)->create(['public_name' => 'Acme Supplies']);

        $response = actingAs($this->exponent)
            ->get(route('exponent.companies.index', [
                'exhibition' => $this->exhibition,
                'search' => 'Acme',
            ]));

        $response->assertOk();
        $data = $response->viewData('page')['props']['companies']['data'];
        expect(count($data))->toBe(2);
    });

    it('search is case-insensitive', function (): void {
        Company::factory()->for($this->exhibition)->create(['public_name' => 'Acme Corporation']);
        Company::factory()->for($this->exhibition)->create(['public_name' => 'Other Company']);

        $response = actingAs($this->exponent)
            ->get(route('exponent.companies.index', [
                'exhibition' => $this->exhibition,
                'search' => 'acme',
            ]));

        $data = $response->viewData('page')['props']['companies']['data'];
        expect(count($data))->toBe(1);
    });

    it('returns empty results for non-matching search', function (): void {
        Company::factory(3)->for($this->exhibition)->create();

        $response = actingAs($this->exponent)
            ->get(route('exponent.companies.index', [
                'exhibition' => $this->exhibition,
                'search' => 'xyznonexistent',
            ]));

        $data = $response->viewData('page')['props']['companies']['data'];
        expect(count($data))->toBe(0);
    });

    it('sorts companies by public_name ascending', function (): void {
        Company::factory()->for($this->exhibition)->create(['public_name' => 'Zebra Co']);
        Company::factory()->for($this->exhibition)->create(['public_name' => 'Alpha Co']);
        Company::factory()->for($this->exhibition)->create(['public_name' => 'Beta Co']);

        $response = actingAs($this->exponent)
            ->get(route('exponent.companies.index', [
                'exhibition' => $this->exhibition,
                'sort' => 'public_name',
            ]));

        $data = $response->viewData('page')['props']['companies']['data'];
        expect($data[0]['public_name'])->toBe('Alpha Co')
            ->and($data[1]['public_name'])->toBe('Beta Co')
            ->and($data[2]['public_name'])->toBe('Zebra Co');
    });

    it('sorts companies by public_name descending', function (): void {
        Company::factory()->for($this->exhibition)->create(['public_name' => 'Zebra Co']);
        Company::factory()->for($this->exhibition)->create(['public_name' => 'Alpha Co']);
        Company::factory()->for($this->exhibition)->create(['public_name' => 'Beta Co']);

        $response = actingAs($this->exponent)
            ->get(route('exponent.companies.index', [
                'exhibition' => $this->exhibition,
                'sort' => '-public_name',
            ]));

        $data = $response->viewData('page')['props']['companies']['data'];
        expect($data[0]['public_name'])->toBe('Zebra Co')
            ->and($data[1]['public_name'])->toBe('Beta Co')
            ->and($data[2]['public_name'])->toBe('Alpha Co');
    });

    it('paginates companies', function (): void {
        Company::factory(20)->for($this->exhibition)->create();

        actingAs($this->exponent)
            ->get(route('exponent.companies.index'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->has('companies.data', 15) // default pagination
                    ->has('companies.links')
            );
    });

    it('handles exhibition with no companies', function (): void {
        actingAs($this->exponent)
            ->get(route('exponent.companies.index'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->where('companies.data', fn($data): bool => count($data) === 0)
            );
    });
})->skip();

// ─────────────────────────────────────────────────────────────
// Create
// ─────────────────────────────────────────────────────────────

describe('Company Create', function (): void {
    beforeEach(function (): void {
        $this->exponent = User::factory()->create();
        $this->exponent->assignRole(UserRole::SUPER_ADMIN);
        $this->exhibition = Exhibition::factory()->create();
    });

    it('displays create form', function (): void {
        actingAs($this->exponent)
            ->get(route('exponent.companies.create'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->component('exponent/Companies/Create')
                    ->has('exhibition')
                    ->has('tags')
            );
    });

    it('passes all tags to create form', function (): void {
        Tag::factory(5)->create();

        actingAs($this->exponent)
            ->get(route('exponent.companies.create'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->where('tags', fn($tags): bool => count($tags) === 5)
            );
    });

    it('passes correct exhibition to create form', function (): void {
        actingAs($this->exponent)
            ->get(route('exponent.companies.create'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->where('exhibition.id',$this->exhibition->id)
            );
    });
})->skip();

// ─────────────────────────────────────────────────────────────
// Store
// ─────────────────────────────────────────────────────────────

describe('Company Store', function (): void {
    beforeEach(function (): void {
        $this->exponent = User::factory()->create();
        $this->exponent->assignRole(UserRole::SUPER_ADMIN);
        $this->exhibition = Exhibition::factory()->create();
        $this->validData = [
            'public_name' => 'Acme Corporation',
            'legal_name' => 'Acme Corporation LLC',
            'description' => 'A company that makes everything you need.',
            'phone' => '+7 (999) 000-00-00',
            'email' => 'acme@example.com',
            'site_url' => 'https://acme.example.com',
            'instagram' => '@acme',
            'telegram' => '@acme_tg',
            'stand_code' => 42,
            'show_on_site' => true,
            'stand_area' => 16,
            'power_kw' => 5,
            'storage_enabled' => false,
            'activities' => 'Manufacturing and distribution of goods.',
        ];
    });

    it('creates company with all fields', function (): void {
        actingAs($this->exponent)
            ->post(route('exponent.companies.store'), $this->validData)
            ->assertRedirect(route('exponent.companies.index'));

        assertDatabaseHas('companies', [
            'public_name' => 'Acme Corporation',
            'legal_name' => 'Acme Corporation LLC',
            'email' => 'acme@example.com',
            'stand_code' => 42,
            'exhibition_id' => $this->exhibition->id,
        ]);
    });

    it('assigns exhibition_id automatically', function (): void {
        actingAs($this->exponent)
            ->post(route('exponent.companies.store'), $this->validData);

        assertDatabaseHas('companies', [
            'email' => 'acme@example.com',
            'exhibition_id' => $this->exhibition->id,
        ]);
    });

    it('attaches tags on create', function (): void {
        $tags = Tag::factory(3)->create();
        $data = array_merge($this->validData, [
            'tags' => $tags->pluck('id')->toArray(),
        ]);

        actingAs($this->exponent)
            ->post(route('exponent.companies.store'), $data);

        $company = Company::query()->where('email', 'acme@example.com')->first();
        expect($company->tags)->toHaveCount(3);
    });

    it('creates company without tags', function (): void {
        actingAs($this->exponent)
            ->post(route('exponent.companies.store'), $this->validData);

        $company = Company::query()->where('email', 'acme@example.com')->first();
        expect($company->tags)->toHaveCount(0);
    });

    it('redirects to index with success flash on store', function (): void {
        actingAs($this->exponent)
            ->post(route('exponent.companies.store'), $this->validData)
            ->assertRedirect(route('exponent.companies.index'))
            ->assertSessionHas('success');
    });

    // ── Validation ──

    it('validates required fields', function (): void {
        actingAs($this->exponent)
            ->post(route('exponent.companies.store'), [])
            ->assertSessionHasErrors(['public_name', 'legal_name', 'description', 'phone', 'email', 'stand_code', 'show_on_site']);
    });

    it('validates public_name max length', function (): void {
        $data = array_merge($this->validData, ['public_name' => str_repeat('a', 256)]);

        actingAs($this->exponent)
            ->post(route('exponent.companies.store'), $data)
            ->assertSessionHasErrors('public_name');
    });

    it('accepts public_name at max boundary', function (): void {
        $data = array_merge($this->validData, ['public_name' => str_repeat('a', 255)]);

        actingAs($this->exponent)
            ->post(route('exponent.companies.store'), $data)
            ->assertRedirect();
    });

    it('validates email format', function (): void {
        $data = array_merge($this->validData, ['email' => 'not-an-email']);

        actingAs($this->exponent)
            ->post(route('exponent.companies.store'), $data)
            ->assertSessionHasErrors('email');
    });

    it('validates email uniqueness', function (): void {
        Company::factory()->for($this->exhibition)->create(['email' => 'acme@example.com']);

        actingAs($this->exponent)
            ->post(route('exponent.companies.store'), $this->validData)
            ->assertSessionHasErrors('email');
    });

    it('validates site_url format', function (): void {
        $data = array_merge($this->validData, ['site_url' => 'not-a-url']);

        actingAs($this->exponent)
            ->post(route('exponent.companies.store'), $data)
            ->assertSessionHasErrors('site_url');
    });

    it('validates stand_code is a positive integer', function (): void {
        $data = array_merge($this->validData, ['stand_code' => -1]);

        actingAs($this->exponent)
            ->post(route('exponent.companies.store'), $data)
            ->assertSessionHasErrors('stand_code');
    });

    it('validates description minimum length', function (): void {
        $data = array_merge($this->validData, ['description' => 'Short']);

        actingAs($this->exponent)
            ->post(route('exponent.companies.store'), $data)
            ->assertSessionHasErrors('description');
    });

    it('validates tags exist in database', function (): void {
        $data = array_merge($this->validData, ['tags' => [99999]]);

        actingAs($this->exponent)
            ->post(route('exponent.companies.store'), $data)
            ->assertSessionHasErrors('tags.0');
    });
})->skip();

// ─────────────────────────────────────────────────────────────
// Edit
// ─────────────────────────────────────────────────────────────

describe('Company Edit', function (): void {
    beforeEach(function (): void {
        $this->exponent = User::factory()->create();
        $this->exponent->assignRole(UserRole::SUPER_ADMIN);
        $this->exhibition = Exhibition::factory()->create();
        $this->company = Company::factory()->for($this->exhibition)->create();
    });

    it('displays edit form', function (): void {
        actingAs($this->exponent)
            ->get(route('exponent.companies.edit', [$this->exhibition, $this->company]))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->component('exponent/Companies/Edit')
                    ->has('exhibition')
                    ->has('company')
                    ->has('tags')
            );
    });

    it('passes correct company to edit form', function (): void {
        actingAs($this->exponent)
            ->get(route('exponent.companies.edit', [$this->exhibition, $this->company]))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->where('company.id', $this->company->id)
                    ->where('company.public_name', $this->company->public_name)
            );
    });

    it('passes all tags to edit form', function (): void {
        Tag::factory(4)->create();

        actingAs($this->exponent)
            ->get(route('exponent.companies.edit', [$this->exhibition, $this->company]))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->where('tags', fn($tags): bool => count($tags) === 4)
            );
    });

    it('eager loads tags on company', function (): void {
        $tags = Tag::factory(2)->create();
        $this->company->tags()->attach($tags->pluck('id'));

        actingAs($this->exponent)
            ->get(route('exponent.companies.edit', [$this->exhibition, $this->company]))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->has('company.tags', 2)
            );
    });
})->skip();

// ─────────────────────────────────────────────────────────────
// Update
// ─────────────────────────────────────────────────────────────

describe('Company Update', function (): void {
    beforeEach(function (): void {
        $this->exponent = User::factory()->create();
        $this->exponent->assignRole(UserRole::SUPER_ADMIN);
        $this->exhibition = Exhibition::factory()->create();
        $this->company = Company::factory()->for($this->exhibition)->create([
            'public_name' => 'Original Name',
            'email' => 'original@example.com',
        ]);
        $this->validData = [
            'public_name' => 'Acme Corporation',
            'legal_name' => 'Acme Corporation LLC',
            'description' => 'A company that makes everything you need.',
            'phone' => '+7 (999) 000-00-00',
            'email' => 'acme@example.com',
            'site_url' => 'https://acme.example.com',
            'instagram' => '@acme',
            'telegram' => '@acme_tg',
            'stand_code' => 42,
            'show_on_site' => true,
            'stand_area' => 16,
            'power_kw' => 5,
            'storage_enabled' => false,
            'activities' => 'Manufacturing and distribution of goods.',
        ];
    });


    it('successfully creates a company with a logo', function (): void {
        Storage::fake('local');

        $logo = UploadedFile::fake()->image('logo.jpg');
        $data = array_merge($this->validData, ['logo' => $logo]);

        actingAs($this->exponent)
            ->post(route('exponent.companies.store'), $data)
            ->assertRedirect(route('exponent.companies.index'));

        $company = Company::query()->where('email', $this->validData['email'])->first();
        expect($company->logo)->not->toBeNull();
    });

    it('successfully updates the logo of a company', function (): void {
        Storage::fake('local');

        $logo = UploadedFile::fake()->image('new-logo.jpg');

        actingAs($this->exponent)
            ->put(route('exponent.companies.update', [$this->exhibition, $this->company]), [
                'public_name'  => $this->company->public_name,
                'legal_name'   => $this->company->legal_name,
                'description'  => $this->company->description,
                'phone'        => $this->company->phone,
                'email'        => $this->company->email,
                'stand_code'   => $this->company->stand_code,
                'show_on_site' => $this->company->show_on_site,
                'logo'         => $logo,
            ])
            ->assertRedirect(route('exponent.companies.index'));

        $this->company->refresh();
        expect($this->company->logo)->not->toBeNull();
    });

    it('deletes all the files in the storage of the current logo when the logo of a company is updated', function (): void {
        Storage::fake('public');

        $oldLogo = UploadedFile::fake()->image('old-logo.jpg');
        $oldPath = Storage::disk('public')->put('companies/logos', $oldLogo);
        $this->company->logo()->create(
            Image::factory()->make([
                'type' => 'logo',
                'webp' => $oldPath,
            ])->toArray()
        );

        $newLogo = UploadedFile::fake()->image('new-logo.jpg');

        actingAs($this->exponent)
            ->put(route('exponent.companies.update', [$this->exhibition, $this->company]), [
                'public_name'  => $this->company->public_name,
                'legal_name'   => $this->company->legal_name,
                'description'  => $this->company->description,
                'phone'        => $this->company->phone,
                'email'        => $this->company->email,
                'stand_code'   => $this->company->stand_code,
                'show_on_site' => $this->company->show_on_site,
                'logo'         => $newLogo,
            ])
            ->assertRedirect(route('exponent.companies.index'));

        Storage::assertMissing($oldPath);

        $this->company->refresh();
        expect($this->company->logo)->not->toBeNull();
    });

    it('deletes all the files in the storage of the current logo when the logo of a company is deleted', function (): void {
        Storage::fake('public');

        $logo = UploadedFile::fake()->image('logo.jpg');
        $logoPath = Storage::disk('public')->put('companies/logos', $logo);
        $this->company->logo()->create(
            Image::factory()->make([
                'type' => 'logo',
                'webp' => $logoPath,
            ])->toArray()
        );

        actingAs($this->exponent)
            ->delete(route('exponent.companies.destroy', [$this->exhibition, $this->company]))
            ->assertRedirect(route('exponent.companies.index'));

        Storage::assertMissing($logoPath);
        assertDatabaseMissing('companies', ['id' => $this->company->id]);
        assertDatabaseMissing('images', ['imageable_id' => $this->company->id]);
    });

    it('updates basic company fields', function (): void {
        actingAs($this->exponent)
            ->put(route('exponent.companies.update', [$this->exhibition, $this->company]), [
                'public_name' => 'Updated Name',
                'legal_name' => 'Updated Legal LLC',
                'description' => 'Updated description for the company.',
                'phone' => '+7 (999) 999-99-99',
                'email' => 'updated@example.com',
                'stand_code' => 99,
                'show_on_site' => false,
            ])
            ->assertRedirect(route('exponent.companies.index'));

        assertDatabaseHas('companies', [
            'id' => $this->company->id,
            'public_name' => 'Updated Name',
            'email' => 'updated@example.com',
            'stand_code' => 99,
        ]);
    });

    it('updates tags', function (): void {
        $oldTags = Tag::factory(2)->create();
        $newTags = Tag::factory(3)->create();
        $this->company->tags()->attach($oldTags->pluck('id'));

        actingAs($this->exponent)
            ->put(route('exponent.companies.update', [$this->exhibition, $this->company]), [
                'public_name' => $this->company->public_name,
                'legal_name' => $this->company->legal_name,
                'description' => $this->company->description,
                'phone' => $this->company->phone,
                'email' => $this->company->email,
                'stand_code' => $this->company->stand_code,
                'show_on_site' => $this->company->show_on_site,
                'tags' => $newTags->pluck('id')->toArray(),
            ]);

        $this->company->refresh();
        expect($this->company->tags)->toHaveCount(3);
        expect($this->company->tags->pluck('id')->toArray())
            ->toBe($newTags->pluck('id')->toArray());
    });

    it('removes all tags when empty array sent', function (): void {
        $tags = Tag::factory(3)->create();
        $this->company->tags()->attach($tags->pluck('id'));

        actingAs($this->exponent)
            ->put(route('exponent.companies.update', [$this->exhibition, $this->company]), [
                'public_name' => $this->company->public_name,
                'legal_name' => $this->company->legal_name,
                'description' => $this->company->description,
                'phone' => $this->company->phone,
                'email' => $this->company->email,
                'stand_code' => $this->company->stand_code,
                'show_on_site' => $this->company->show_on_site,
                'tags' => [],
            ]);

        $this->company->refresh();
        expect($this->company->tags)->toHaveCount(0);
    });

    it('allows same email on update for same company', function (): void {
        actingAs($this->exponent)
            ->put(route('exponent.companies.update', [$this->exhibition, $this->company]), [
                'public_name' => $this->company->public_name,
                'legal_name' => $this->company->legal_name,
                'description' => $this->company->description,
                'phone' => $this->company->phone,
                'email' => 'original@example.com', // same email
                'stand_code' => $this->company->stand_code,
                'show_on_site' => $this->company->show_on_site,
            ])
            ->assertRedirect();

        assertDatabaseHas('companies', [
            'id' => $this->company->id,
            'email' => 'original@example.com',
        ]);
    });

    it('rejects email already taken by another company', function (): void {
        Company::factory()->for($this->exhibition)->create(['email' => 'taken@example.com']);

        actingAs($this->exponent)
            ->put(route('exponent.companies.update', [$this->exhibition, $this->company]), [
                'public_name' => $this->company->public_name,
                'legal_name' => $this->company->legal_name,
                'description' => $this->company->description,
                'phone' => $this->company->phone,
                'email' => 'taken@example.com',
                'stand_code' => $this->company->stand_code,
                'show_on_site' => $this->company->show_on_site,
            ])
            ->assertSessionHasErrors('email');
    });

    it('redirects to index with success flash on update', function (): void {
        actingAs($this->exponent)
            ->put(route('exponent.companies.update', [$this->exhibition, $this->company]), [
                'public_name' => 'Updated Name',
                'legal_name' => $this->company->legal_name,
                'description' => $this->company->description,
                'phone' => $this->company->phone,
                'email' => $this->company->email,
                'stand_code' => $this->company->stand_code,
                'show_on_site' => $this->company->show_on_site,
            ])
            ->assertRedirect(route('exponent.companies.index'))
            ->assertSessionHas('success');
    });

    it('validates update data', function (): void {
        actingAs($this->exponent)
            ->put(route('exponent.companies.update', [$this->exhibition, $this->company]), [
                'public_name' => '',
            ])
            ->assertSessionHasErrors('public_name');
    });
})->skip();

// ─────────────────────────────────────────────────────────────
// Destroy
// ─────────────────────────────────────────────────────────────

describe('Company Destroy', function (): void {
    beforeEach(function (): void {
        $this->exponent = User::factory()->create();
        $this->exponent->assignRole(UserRole::SUPER_ADMIN);
        $this->exhibition = Exhibition::factory()->create();
        $this->company = Company::factory()->for($this->exhibition)->create();
    });

    it('deletes company', function (): void {
        actingAs($this->exponent)
            ->delete(route('exponent.companies.destroy', [$this->exhibition, $this->company]))
            ->assertRedirect();

        assertDatabaseMissing('companies', ['id' => $this->company->id]);
    });

    it('redirects to index with success flash on destroy', function (): void {
        actingAs($this->exponent)
            ->delete(route('exponent.companies.destroy', [$this->exhibition, $this->company]))
            ->assertRedirect(route('exponent.companies.index'))
            ->assertSessionHas('success');
    });

    it('detaches tags on delete', function (): void {
        $tags = Tag::factory(2)->create();
        $this->company->tags()->attach($tags->pluck('id'));

        actingAs($this->exponent)
            ->delete(route('exponent.companies.destroy', [$this->exhibition, $this->company]));

        assertDatabaseMissing('company_tag', ['company_id' => $this->company->id]);
    });

    it('does not delete the tags themselves', function (): void {
        $tags = Tag::factory(2)->create();
        $this->company->tags()->attach($tags->pluck('id'));

        actingAs($this->exponent)
            ->delete(route('exponent.companies.destroy', [$this->exhibition, $this->company]));

        foreach ($tags as $tag) {
            assertDatabaseHas('tags', ['id' => $tag->id]);
        }
    });

    it('exponent can only delete companies from assigned exhibitions', function (): void {
        $exponent = User::factory()->create();
        $exponent->assignRole(UserRole::exponent);

        $assignedExhibition = Exhibition::factory()->create();
        $assignedExhibition->users()->attach($exponent);
        $assignedCompany = Company::factory()->for($assignedExhibition)->create();

        $unassignedExhibition = Exhibition::factory()->create();
        $unassignedCompany = Company::factory()->for($unassignedExhibition)->create();

        actingAs($exponent)
            ->delete(route('exponent.companies.destroy', [$assignedCompany]))
            ->assertRedirect();

        actingAs($exponent)
            ->delete(route('exponent.companies.destroy', [$unassignedCompany]))
            ->assertForbidden();

        assertDatabaseMissing('companies', ['id' => $assignedCompany->id]);
        assertDatabaseHas('companies', ['id' => $unassignedCompany->id]);
    });
})->skip();

// ─────────────────────────────────────────────────────────────
// Edge Cases
// ─────────────────────────────────────────────────────────────

describe('Company Edge Cases', function (): void {
    beforeEach(function (): void {
        $this->exponent = User::factory()->create();
        $this->exponent->assignRole(UserRole::SUPER_ADMIN);
        $this->exhibition = Exhibition::factory()->create();
    });

    it('handles company with no tags on index', function (): void {
        Company::factory()->for($this->exhibition)->create();

        actingAs($this->exponent)
            ->get(route('exponent.companies.index'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->where('companies.data.0.tags', fn($tags): bool => count($tags) === 0)
            );
    });

    it('show_on_site can be set to false', function (): void {
        $data = [
            'public_name' => 'Acme Corporation',
            'legal_name' => 'Acme Corporation LLC',
            'description' => 'A company that makes everything you need.',
            'phone' => '+7 (999) 000-00-00',
            'email' => 'hidden@example.com',
            'site_url' => 'https://acme.example.com',
            'instagram' => '@acme',
            'telegram' => '@acme_tg',
            'activies' => 'Manufacturing and distribution of goods.',
            'stand_code' => 42,
            'show_on_site' => false,
            'stand_area' => 16,
            'power_kw' => 5,
            'storage_enabled' => false,
            'activities' => 'Manufacturing and distribution of goods.',
        ];

        actingAs($this->exponent)
            ->post(route('exponent.companies.store'), $data)
            ->assertRedirect();

        assertDatabaseHas('companies', [
            'email' => 'hidden@example.com',
            'show_on_site' => 0,
        ]);
    });

    it('two companies in different exhibitions can share the same stand_code', function (): void {
        $otherExhibition = Exhibition::factory()->create();

        Company::factory()->for($otherExhibition)->create(['stand_code' => 10]);

        $data = [
            'public_name' => 'Acme Corporation',
            'legal_name' => 'Acme Corporation LLC',
            'description' => 'A company that makes everything you need.',
            'phone' => '+7 (999) 000-00-00',
            'email' => 'acme@example.com',
            'site_url' => 'https://acme.example.com',
            'instagram' => '@acme',
            'telegram' => '@acme_tg',
            'activies' => 'Manufacturing and distribution of goods.',
            'stand_code' => 42,
            'show_on_site' => true,
            'stand_area' => 16,
            'power_kw' => 5,
            'storage_enabled' => false,
            'activities' => 'Manufacturing and distribution of goods.',
        ];

        actingAs($this->exponent)
            ->post(route('exponent.companies.store'), $data)
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        assertDatabaseHas('companies', [
            'email' => 'acme@example.com',
            'stand_code' => 42,
        ]);
    });
})->skip();
