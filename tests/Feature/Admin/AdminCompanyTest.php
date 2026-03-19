<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Exhibition;
use App\Models\Image;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\get;

// ─────────────────────────────────────────────────────────────
// Access Control
// ─────────────────────────────────────────────────────────────

describe('Admin Company Test - Access Control', function (): void {
    beforeEach(function (): void {
        $this->exhibition = Exhibition::factory()->create();
        $this->company = Company::factory()->for($this->exhibition)->create();
    });

    it('redirects guest users to login on index', function (): void {
        get(route('admin.companies.index'))
            ->assertRedirect(route('login'));
    });

    it('redirects guest users to login on create', function (): void {
        get(route('admin.companies.create'))
            ->assertRedirect(route('login'));
    });

    it('redirects guest users to login on edit', function (): void {
        get(route('admin.companies.edit', $this->company))
            ->assertRedirect(route('login'));
    });

    it('forbids USER role from accessing companies', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);

        actingAs($user)
            ->get(route('admin.companies.index'))
            ->assertForbidden();
    });

    it('forbids EXPONENT role from accessing companies', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::EXPONENT);

        actingAs($user)
            ->get(route('admin.companies.index'))
            ->assertForbidden();
    });

    it('super admin can access all exhibitions companies', function (): void {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(UserRole::SUPER_ADMIN);

        actingAs($superAdmin)
            ->get(route('admin.companies.index'))
            ->assertOk();
    });
});

// ─────────────────────────────────────────────────────────────
// Index
// ─────────────────────────────────────────────────────────────

describe('Company Index', function (): void {
    beforeEach(function (): void {
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole(UserRole::SUPER_ADMIN);
        $this->exhibition = Exhibition::factory()->create();
    });

    it('displays all companies for an exhibition', function (): void {
        Company::factory(5)->for($this->exhibition)->create();

        actingAs($this->superAdmin)
            ->get(route('admin.companies.index'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->component('admin/Companies/Index')
                    ->has('companies.data', 5)
            );
    });

    it('only shows companies belonging to the given exhibition', function (): void {
        Company::factory(3)->for($this->exhibition)->create();
        $otherExhibition = Exhibition::factory()->create();
        Company::factory(4)->for($otherExhibition)->create();

        actingAs($this->superAdmin)
            ->get(route('admin.companies.index'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page->has('companies.data', 3)
            );
    });

    it('passes exhibition to the view', function (): void {
        actingAs($this->superAdmin)
            ->get(route('admin.companies.index'))
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

        actingAs($this->superAdmin)
            ->get(route('admin.companies.index'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page->has('companies.data.0.tags')
            );
    });

    it('searches companies by public_name', function (): void {
        Company::factory()->for($this->exhibition)->create(['public_name' => 'Acme Corporation']);
        Company::factory()->for($this->exhibition)->create(['public_name' => 'Globex Industries']);
        Company::factory()->for($this->exhibition)->create(['public_name' => 'Acme Supplies']);

        $data = actingAs($this->superAdmin)
            ->get(route('admin.companies.index', ['search' => 'Acme']))
            ->assertOk()
            ->viewData('page')['props']['companies']['data'];

        expect(count($data))->toBe(2);
    });

    it('search is case-insensitive', function (): void {
        Company::factory()->for($this->exhibition)->create(['public_name' => 'Acme Corporation']);
        Company::factory()->for($this->exhibition)->create(['public_name' => 'Other Company']);

        $data = actingAs($this->superAdmin)
            ->get(route('admin.companies.index', ['search' => 'acme']))
            ->viewData('page')['props']['companies']['data'];

        expect(count($data))->toBe(1);
    });

    it('returns empty results for non-matching search', function (): void {
        Company::factory(3)->for($this->exhibition)->create();

        $data = actingAs($this->superAdmin)
            ->get(route('admin.companies.index', ['search' => 'xyznonexistent']))
            ->viewData('page')['props']['companies']['data'];

        expect(count($data))->toBe(0);
    });

    it('sorts companies by public_name ascending', function (): void {
        Company::factory()->for($this->exhibition)->create(['public_name' => 'Zebra Co']);
        Company::factory()->for($this->exhibition)->create(['public_name' => 'Alpha Co']);
        Company::factory()->for($this->exhibition)->create(['public_name' => 'Beta Co']);

        $data = actingAs($this->superAdmin)
            ->get(route('admin.companies.index', ['sort' => 'public_name']))
            ->viewData('page')['props']['companies']['data'];

        expect($data[0]['public_name'])->toBe('Alpha Co')
            ->and($data[1]['public_name'])->toBe('Beta Co')
            ->and($data[2]['public_name'])->toBe('Zebra Co');
    });

    it('sorts companies by public_name descending', function (): void {
        Company::factory()->for($this->exhibition)->create(['public_name' => 'Zebra Co']);
        Company::factory()->for($this->exhibition)->create(['public_name' => 'Alpha Co']);
        Company::factory()->for($this->exhibition)->create(['public_name' => 'Beta Co']);

        $data = actingAs($this->superAdmin)
            ->get(route('admin.companies.index', ['sort' => '-public_name']))
            ->viewData('page')['props']['companies']['data'];

        expect($data[0]['public_name'])->toBe('Zebra Co')
            ->and($data[1]['public_name'])->toBe('Beta Co')
            ->and($data[2]['public_name'])->toBe('Alpha Co');
    });

    it('paginates companies', function (): void {
        Company::factory(20)->for($this->exhibition)->create();

        actingAs($this->superAdmin)
            ->get(route('admin.companies.index'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->has('companies.data', 15)
                    ->has('companies.links')
            );
    });

    it('handles exhibition with no companies', function (): void {
        actingAs($this->superAdmin)
            ->get(route('admin.companies.index'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->where('companies.data', fn($data): bool => count($data) === 0)
            );
    });

    it('handles company with no tags on index', function (): void {
        Company::factory()->for($this->exhibition)->create();

        actingAs($this->superAdmin)
            ->get(route('admin.companies.index'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->where('companies.data.0.tags', fn($tags): bool => count($tags) === 0)
            );
    });
});

// ─────────────────────────────────────────────────────────────
// Create
// ─────────────────────────────────────────────────────────────

describe('Company Create', function (): void {
    beforeEach(function (): void {
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole(UserRole::SUPER_ADMIN);
        $this->exhibition = Exhibition::factory()->create();
    });

    it('displays create form', function (): void {
        actingAs($this->superAdmin)
            ->get(route('admin.companies.create'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->component('admin/Companies/Create')
                    ->has('exhibition')
                    ->has('tags')
            );
    });

    it('passes all tags to create form', function (): void {
        Tag::factory(5)->create();

        actingAs($this->superAdmin)
            ->get(route('admin.companies.create'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->where('tags', fn($tags): bool => count($tags) === 5)
            );
    });

    it('passes correct exhibition to create form', function (): void {
        actingAs($this->superAdmin)
            ->get(route('admin.companies.create'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->where('exhibition.id', $this->exhibition->id)
            );
    });
});

// ─────────────────────────────────────────────────────────────
// Store
// ─────────────────────────────────────────────────────────────

describe('Company Store', function (): void {
    beforeEach(function (): void {
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole(UserRole::SUPER_ADMIN);
        $this->exhibition = Exhibition::factory()->create();

        $this->validData = [
            'public_name'     => 'Acme Corporation',
            'legal_name'      => 'Acme Corporation LLC',
            'stand_code'      => '52-CND',
            'show_on_site'    => true,
            'stand_area'      => 16,
            'power_kw'        => 5,
            'storage_enabled' => false,
        ];
    });

    it('creates company with only required fields', function (): void {
        actingAs($this->superAdmin)
            ->post(route('admin.companies.store'), $this->validData)
            ->assertRedirect(route('admin.companies.index'));

        assertDatabaseHas('companies', [
            'public_name'   => 'Acme Corporation',
            'legal_name'    => 'Acme Corporation LLC',
            'stand_code'    => '52-CND',
            'exhibition_id' => $this->exhibition->id,
        ]);
    });

    it('creates company with all fields', function (): void {
        actingAs($this->superAdmin)
            ->post(route('admin.companies.store'), array_merge($this->validData, [
                'description' => 'A company that makes everything.',
                'phone'       => '+7 (999) 000-00-00',
                'email'       => 'acme@example.com',
                'site_url'    => 'https://acme.example.com',
                'instagram'   => '@acme',
                'telegram'    => '@acme_tg',
                'activities'  => 'Manufacturing and distribution.',
            ]))
            ->assertRedirect(route('admin.companies.index'));

        assertDatabaseHas('companies', [
            'public_name'   => 'Acme Corporation',
            'stand_code'    => '52-CND',
            'exhibition_id' => $this->exhibition->id,
        ]);
    });

    it('assigns exhibition_id automatically', function (): void {
        actingAs($this->superAdmin)
            ->post(route('admin.companies.store'), $this->validData);

        assertDatabaseHas('companies', [
            'public_name'   => 'Acme Corporation',
            'exhibition_id' => $this->exhibition->id,
        ]);
    });

    it('attaches tags on create', function (): void {
        $tags = Tag::factory(3)->create();

        actingAs($this->superAdmin)
            ->post(route('admin.companies.store'), array_merge($this->validData, [
                'tags' => $tags->pluck('id')->toArray(),
            ]));

        $company = Company::query()->where('public_name', 'Acme Corporation')->first();
        expect($company->tags)->toHaveCount(3);
    });

    it('creates company without tags', function (): void {
        actingAs($this->superAdmin)
            ->post(route('admin.companies.store'), $this->validData);

        $company = Company::query()->where('public_name', 'Acme Corporation')->first();
        expect($company->tags)->toHaveCount(0);
    });

    it('show_on_site can be set to false', function (): void {
        actingAs($this->superAdmin)
            ->post(route('admin.companies.store'), array_merge($this->validData, [
                'show_on_site' => false,
            ]))
            ->assertRedirect();

        assertDatabaseHas('companies', [
            'public_name'  => 'Acme Corporation',
            'show_on_site' => 0,
        ]);
    });

    it('storage_enabled can be set to true', function (): void {
        actingAs($this->superAdmin)
            ->post(route('admin.companies.store'), array_merge($this->validData, [
                'storage_enabled' => true,
            ]))
            ->assertRedirect();

        assertDatabaseHas('companies', [
            'public_name'     => 'Acme Corporation',
            'storage_enabled' => 1,
        ]);
    });

    it('two companies in different exhibitions can share the same stand_code', function (): void {
        $otherExhibition = Exhibition::factory()->create();
        Company::factory()->for($otherExhibition)->create(['stand_code' => '52-CND']);

        actingAs($this->superAdmin)
            ->post(route('admin.companies.store'), $this->validData)
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        assertDatabaseHas('companies', [
            'public_name' => 'Acme Corporation',
            'stand_code'  => '52-CND',
        ]);
    });

    it('redirects to index with success flash on store', function (): void {
        actingAs($this->superAdmin)
            ->post(route('admin.companies.store'), $this->validData)
            ->assertRedirect(route('admin.companies.index'))
            ->assertSessionHas('success');
    });

    // ── Validation ──

    it('validates required fields', function (): void {
        actingAs($this->superAdmin)
            ->post(route('admin.companies.store'), [])
            ->assertSessionHasErrors([
                'public_name',
                'legal_name',
                'stand_code',
                'show_on_site',
                'stand_area',
                'power_kw',
                'storage_enabled',
            ]);
    });

    it('validates public_name max length', function (): void {
        actingAs($this->superAdmin)
            ->post(route('admin.companies.store'), array_merge($this->validData, [
                'public_name' => str_repeat('a', 256),
            ]))
            ->assertSessionHasErrors('public_name');
    });

    it('accepts public_name at max boundary', function (): void {
        actingAs($this->superAdmin)
            ->post(route('admin.companies.store'), array_merge($this->validData, [
                'public_name' => str_repeat('a', 255),
            ]))
            ->assertRedirect();
    });

    it('validates tags exist in database', function (): void {
        actingAs($this->superAdmin)
            ->post(route('admin.companies.store'), array_merge($this->validData, [
                'tags' => [99999],
            ]))
            ->assertSessionHasErrors('tags.0');
    });
});

// ─────────────────────────────────────────────────────────────
// Edit
// ─────────────────────────────────────────────────────────────

describe('Company Edit', function (): void {
    beforeEach(function (): void {
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole(UserRole::SUPER_ADMIN);
        $this->exhibition = Exhibition::factory()->create();
        $this->company = Company::factory()->for($this->exhibition)->create();
    });

    it('displays edit form', function (): void {
        actingAs($this->superAdmin)
            ->get(route('admin.companies.edit', $this->company))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->component('admin/Companies/Edit')
                    ->has('exhibition')
                    ->has('company')
                    ->has('tags')
            );
    });

    it('passes correct company to edit form', function (): void {
        actingAs($this->superAdmin)
            ->get(route('admin.companies.edit', $this->company))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->where('company.id', $this->company->id)
                    ->where('company.public_name', $this->company->public_name)
            );
    });

    it('passes all tags to edit form', function (): void {
        Tag::factory(4)->create();

        actingAs($this->superAdmin)
            ->get(route('admin.companies.edit', $this->company))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->where('tags', fn($tags): bool => count($tags) === 4)
            );
    });

    it('eager loads tags on company', function (): void {
        $tags = Tag::factory(2)->create();
        $this->company->tags()->attach($tags->pluck('id'));

        actingAs($this->superAdmin)
            ->get(route('admin.companies.edit', $this->company))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page->has('company.tags', 2)
            );
    });
});

// ─────────────────────────────────────────────────────────────
// Update
// ─────────────────────────────────────────────────────────────

describe('Company Update', function (): void {
    beforeEach(function (): void {
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole(UserRole::SUPER_ADMIN);
        $this->exhibition = Exhibition::factory()->create();
        $this->company = Company::factory()->for($this->exhibition)->create([
            'public_name' => 'Original Name',
            'email'       => 'original@example.com',
        ]);

        $this->minData = [
            'public_name'     => $this->company->public_name,
            'legal_name'      => $this->company->legal_name,
            'stand_code'      => $this->company->stand_code,
            'show_on_site'    => $this->company->show_on_site,
            'stand_area'      => $this->company->stand_area,
            'power_kw'        => $this->company->power_kw,
            'storage_enabled' => $this->company->storage_enabled,
        ];
    });

    it('updates basic company fields', function (): void {
        actingAs($this->superAdmin)
            ->put(route('admin.companies.update', $this->company), [
                'public_name'     => 'Updated Name',
                'legal_name'      => 'Updated Legal LLC',
                'stand_code'      => '5-CDN',
                'show_on_site'    => false,
                'stand_area'      => 20,
                'power_kw'        => 10,
                'storage_enabled' => true,
            ])
            ->assertRedirect(route('admin.companies.index'));

        assertDatabaseHas('companies', [
            'id'          => $this->company->id,
            'public_name' => 'Updated Name',
            'stand_code'  => '5-CDN',
        ]);
    });

    it('updates tags', function (): void {
        $oldTags = Tag::factory(2)->create();
        $newTags = Tag::factory(3)->create();
        $this->company->tags()->attach($oldTags->pluck('id'));

        actingAs($this->superAdmin)
            ->put(route('admin.companies.update', $this->company), array_merge($this->minData, [
                'tags' => $newTags->pluck('id')->toArray(),
            ]));

        $this->company->refresh();
        expect($this->company->tags)->toHaveCount(3)
            ->and($this->company->tags->pluck('id')->toArray())->toBe($newTags->pluck('id')->toArray());
    });

    it('removes all tags when empty array sent', function (): void {
        $tags = Tag::factory(3)->create();
        $this->company->tags()->attach($tags->pluck('id'));

        actingAs($this->superAdmin)
            ->put(route('admin.companies.update', $this->company), array_merge($this->minData, [
                'tags' => [],
            ]));

        $this->company->refresh();
        expect($this->company->tags)->toHaveCount(0);
    });

    it('allows same email on update for same company', function (): void {
        actingAs($this->superAdmin)
            ->put(route('admin.companies.update', $this->company), array_merge($this->minData, [
                'email' => 'original@example.com',
            ]))
            ->assertRedirect();

        assertDatabaseHas('companies', [
            'id'    => $this->company->id,
            'email' => 'original@example.com',
        ]);
    });

    it('redirects to index with success flash on update', function (): void {
        actingAs($this->superAdmin)
            ->put(route('admin.companies.update', $this->company), array_merge($this->minData, [
                'public_name' => 'Updated Name',
            ]))
            ->assertRedirect(route('admin.companies.index'));
    });

    it('validates update data', function (): void {
        actingAs($this->superAdmin)
            ->put(route('admin.companies.update', $this->company), [
                'public_name' => '',
            ])
            ->assertSessionHasErrors('public_name');
    });
});

// ─────────────────────────────────────────────────────────────
// Destroy
// ─────────────────────────────────────────────────────────────

describe('Company Destroy', function (): void {
    beforeEach(function (): void {
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole(UserRole::SUPER_ADMIN);
        $this->exhibition = Exhibition::factory()->create();
        $this->company = Company::factory()->for($this->exhibition)->create();
    });

    it('deletes company', function (): void {
        actingAs($this->superAdmin)
            ->delete(route('admin.companies.destroy', $this->company))
            ->assertRedirect();

        assertDatabaseMissing('companies', ['id' => $this->company->id]);
    });

    it('redirects to index with success flash on destroy', function (): void {
        actingAs($this->superAdmin)
            ->delete(route('admin.companies.destroy', $this->company))
            ->assertRedirect(route('admin.companies.index'))
            ->assertSessionHas('success');
    });

    it('detaches tags on delete', function (): void {
        $tags = Tag::factory(2)->create();
        $this->company->tags()->attach($tags->pluck('id'));

        actingAs($this->superAdmin)
            ->delete(route('admin.companies.destroy', $this->company));

        assertDatabaseMissing('company_tag', ['company_id' => $this->company->id]);
    });

    it('does not delete the tags themselves', function (): void {
        $tags = Tag::factory(2)->create();
        $this->company->tags()->attach($tags->pluck('id'));

        actingAs($this->superAdmin)
            ->delete(route('admin.companies.destroy', $this->company));

        foreach ($tags as $tag) {
            assertDatabaseHas('tags', ['id' => $tag->id]);
        }
    });

    it('deletes all the files in the storage of the current logo when the logo of a company is deleted', function (): void {
        Storage::fake('public');

        $logoPath = Storage::disk('public')->put('companies/logos', UploadedFile::fake()->image('logo.jpg'));
        $this->company->logo()->create(
            Image::factory()->make(['type' => 'logo', 'webp' => $logoPath])->toArray()
        );

        actingAs($this->superAdmin)
            ->delete(route('admin.companies.destroy', $this->company))
            ->assertRedirect(route('admin.companies.index'));

        Storage::disk('public')->assertMissing($logoPath);
        assertDatabaseMissing('companies', ['id' => $this->company->id]);
        assertDatabaseMissing('images', ['imageable_id' => $this->company->id]);
    });

    it('admin can only delete companies from assigned exhibitions', function (): void {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);

        $assignedExhibition = Exhibition::factory()->create();
        $assignedExhibition->users()->attach($admin);
        $assignedCompany = Company::factory()->for($assignedExhibition)->create();

        $unassignedExhibition = Exhibition::factory()->create();
        $unassignedCompany = Company::factory()->for($unassignedExhibition)->create();

        actingAs($admin)
            ->delete(route('admin.companies.destroy', $assignedCompany))
            ->assertRedirect();

        actingAs($admin)
            ->delete(route('admin.companies.destroy', $unassignedCompany))
            ->assertForbidden();

        assertDatabaseMissing('companies', ['id' => $assignedCompany->id]);
        assertDatabaseHas('companies', ['id' => $unassignedCompany->id]);
    });
});
