<?php
declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Exhibition;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;

describe('Exponent Company Test', function (): void {
    beforeEach(function (): void {
        $this->exhibition = Exhibition::factory()->create();
        $this->company = Company::factory()->for($this->exhibition)->create([
            'email' => 'company@example.com',
        ]);
        $this->exponent = User::factory()->for($this->company)->create();
        $this->exponent->assignRole(UserRole::EXPONENT);
    });

    it('allows exponent with a company to enter this page', function (): void {
        actingAs($this->exponent)
            ->get(route('exponent.companies.index'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page->component('exponent/Companies/Index')
                    ->has('company')
            );
    });

    it('forbids admin from entering this page', function (): void {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);

        actingAs($admin)
            ->get(route('exponent.companies.index'))
            ->assertForbidden();
    });

    it('forbids super admin from entering this page', function (): void {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(UserRole::SUPER_ADMIN);

        actingAs($superAdmin)
            ->get(route('exponent.companies.index'))
            ->assertForbidden();
    });

    it('forbids user from entering this page', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);

        actingAs($user)
            ->get(route('exponent.companies.index'))
            ->assertForbidden();
    });

    it('forbids guest user from entering this page', function (): void {
        get(route('exponent.companies.index'))
            ->assertRedirect(route('login'));
    });

    it('displays the company with all the attributes on the index page', function (): void {
        actingAs($this->exponent)
            ->get(route('exponent.companies.index'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->component('exponent/Companies/Index')
                    ->where('company.id', $this->company->id)
                    ->where('company.public_name', $this->company->public_name)
                    ->where('company.email', $this->company->email)
                    ->has('company.tags')
            );
    });

    it('successfully renders company edit page', function (): void {
        actingAs($this->exponent)
            ->get(route('exponent.companies.edit', $this->company))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->component('exponent/Companies/Edit')
                    ->has('company')
                    ->where('company.id', $this->company->id)
            );
    });

    it('successfully updates company logo', function (): void {
        Storage::fake('local');

        actingAs($this->exponent)
            ->put(route('exponent.companies.update', $this->company), [
                'logo' => UploadedFile::fake()->image('logo.jpg'),
            ])
            ->assertRedirect(route('exponent.companies.index'));

        $this->company->refresh();
        expect($this->company->logo)->not->toBeNull();
    });

    it('successfully updates company data', function (): void {
        actingAs($this->exponent)
            ->put(route('exponent.companies.update', $this->company), [
                'public_name' => 'Updated Company Name',
                'description' => 'This is a valid updated description for the company.',
                'phone'       => '+7 (999) 123-45-67',
                'email'       => 'updated@example.com',
            ])
            ->assertRedirect(route('exponent.companies.index'));

        assertDatabaseHas('companies', [
            'id'          => $this->company->id,
            'public_name' => 'Updated Company Name',
            'email'       => 'updated@example.com',
        ]);
    });

    // ── Validation ──

    it('validates public_name max length', function (): void {
        actingAs($this->exponent)
            ->put(route('exponent.companies.update', $this->company), [
                'public_name' => str_repeat('a', 256),
            ])
            ->assertSessionHasErrors('public_name');
    });

    it('validates description min length', function (): void {
        actingAs($this->exponent)
            ->put(route('exponent.companies.update', $this->company), [
                'description' => 'Short',
            ])
            ->assertSessionHasErrors('description');
    });

    it('validates description max length', function (): void {
        actingAs($this->exponent)
            ->put(route('exponent.companies.update', $this->company), [
                'description' => str_repeat('a', 5001),
            ])
            ->assertSessionHasErrors('description');
    });

    it('validates phone max length', function (): void {
        actingAs($this->exponent)
            ->put(route('exponent.companies.update', $this->company), [
                'phone' => str_repeat('1', 51),
            ])
            ->assertSessionHasErrors('phone');
    });

    it('validates email format', function (): void {
        actingAs($this->exponent)
            ->put(route('exponent.companies.update', $this->company), [
                'email' => 'not-a-valid-email',
            ])
            ->assertSessionHasErrors('email');
    });

    it('validates email uniqueness', function (): void {
        $otherCompany = Company::factory()->for($this->exhibition)->create([
            'email' => 'taken@example.com',
        ]);

        actingAs($this->exponent)
            ->put(route('exponent.companies.update', $this->company), [
                'email' => 'taken@example.com',
            ])
            ->assertSessionHasErrors('email');
    });

    it('allows same email on update for the same company', function (): void {
        actingAs($this->exponent)
            ->put(route('exponent.companies.update', $this->company), [
                'email' => 'company@example.com',
            ])
            ->assertRedirect(route('exponent.companies.index'));

        assertDatabaseHas('companies', [
            'id'    => $this->company->id,
            'email' => 'company@example.com',
        ]);
    });

    it('validates site_url format', function (): void {
        actingAs($this->exponent)
            ->put(route('exponent.companies.update', $this->company), [
                'site_url' => 'not-a-valid-url',
            ])
            ->assertSessionHasErrors('site_url');
    });

    it('validates site_url max length', function (): void {
        actingAs($this->exponent)
            ->put(route('exponent.companies.update', $this->company), [
                'site_url' => 'https://' . str_repeat('a', 250) . '.com',
            ])
            ->assertSessionHasErrors('site_url');
    });

    it('validates instagram max length', function (): void {
        actingAs($this->exponent)
            ->put(route('exponent.companies.update', $this->company), [
                'instagram' => str_repeat('a', 256),
            ])
            ->assertSessionHasErrors('instagram');
    });

    it('validates telegram max length', function (): void {
        actingAs($this->exponent)
            ->put(route('exponent.companies.update', $this->company), [
                'telegram' => str_repeat('a', 256),
            ])
            ->assertSessionHasErrors('telegram');
    });

    it('validates activities max length', function (): void {
        actingAs($this->exponent)
            ->put(route('exponent.companies.update', $this->company), [
                'activities' => str_repeat('a', 5001),
            ])
            ->assertSessionHasErrors('activities');
    });

    it('validates logo is an image', function (): void {
        actingAs($this->exponent)
            ->put(route('exponent.companies.update', $this->company), [
                'logo' => UploadedFile::fake()->create('document.pdf', 100),
            ])
            ->assertSessionHasErrors('logo');
    });

    it('validates logo max size', function (): void {
        actingAs($this->exponent)
            ->put(route('exponent.companies.update', $this->company), [
                'logo' => UploadedFile::fake()->create('logo.jpg', 60000),
            ])
            ->assertSessionHasErrors('logo');
    });

    it('forbids exponent from updating another company', function (): void {
        $otherCompany = Company::factory()->for($this->exhibition)->create();

        actingAs($this->exponent)
            ->put(route('exponent.companies.update', $otherCompany), [
                'public_name' => 'Hacked Name',
            ])
            ->assertForbidden();

        \Pest\Laravel\assertDatabaseMissing('companies', [
            'id'          => $otherCompany->id,
            'public_name' => 'Hacked Name',
        ]);
    });
});
