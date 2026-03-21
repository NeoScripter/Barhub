<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Exhibition;
use App\Models\Service;
use App\Models\User;

use function Pest\Laravel\actingAs;

describe('Admin Service Test', function (): void {

    beforeEach(function (): void {
        $this->user = User::factory()->create([
            'email'    => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $this->user->assignRole(UserRole::SUPER_ADMIN);
        $this->exhibition = Exhibition::factory()->create();
        $this->user->setActiveExhibition($this->exhibition->id);
        $this->route = '/admin/services';
    });

    it('renders the service index page', function (): void {
        $services = Service::factory(3)->for($this->exhibition)->create();

        $page = visit('/login');
        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($this->route);
        $page->assertSee('Услуги');
        $page->assertSee($services[0]->name);
    });

    it('doesnt allow to create a service when the name is too long', function (): void {
        $page = visit('/login');
        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($this->route)
            ->click('@create-service')
            ->assertSee('Название')
            ->fill('name', generateTextWithChars(204))
            ->fill('description', generateTextWithChars(5004))
            ->fill('placeholder', generateTextWithChars(50))
            ->submit()
            ->assertSee('Название не должно превышать 200 символов');
    });

    it('doesnt allow to create a service when the description is too short', function (): void {
        $page = visit('/login');
        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($this->route)
            ->click('@create-service')
            ->assertSee('Название')
            ->fill('name', generateTextWithChars(50))
            ->fill('description', generateTextWithChars(3))
            ->fill('placeholder', generateTextWithChars(40))
            ->submit()
            ->assertSee('Описание должно содержать не менее 10 символов');
    });

    it('doesnt allow to create a service when the description is too long', function (): void {
        $page = visit('/login');
        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($this->route)
            ->click('@create-service')
            ->assertSee('Название')
            ->fill('name', generateTextWithChars(50))
            ->fill('description', generateTextWithChars(5004))
            ->fill('placeholder', generateTextWithChars(40))
            ->submit()
            ->assertSee('Описание не должно превышать 5000 символов');
    });

    it('doesnt allow to create a service when the placeholder is too short', function (): void {
        $page = visit('/login');
        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($this->route)
            ->click('@create-service')
            ->assertSee('Название')
            ->fill('name', generateTextWithChars(50))
            ->fill('description', generateTextWithChars(20))
            ->fill('placeholder', generateTextWithChars(3))
            ->submit()
            ->assertSee('Подсказка должна содержать не менее 10 символов');
    });

    it('doesnt allow to create a service when the placeholder is too long', function (): void {
        $page = visit('/login');
        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($this->route)
            ->click('@create-service')
            ->assertSee('Название')
            ->fill('name', generateTextWithChars(50))
            ->fill('description', generateTextWithChars(100))
            ->fill('placeholder', generateTextWithChars(5005))
            ->submit()
            ->assertSee('Длина подсказки не должна превышать 5000 символов');
    });

    it('allows to create a service with valid data', function (): void {
        $page = visit('/login');
        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($this->route)
            ->click('@create-service')
            ->assertSee('Название')
            ->fill('name', generateTextWithChars(50))
            ->fill('description', generateTextWithChars(20))
            ->fill('placeholder', generateTextWithChars(100))
            ->submit()
            ->assertPathEndsWith($this->route);
    });

    it('doesnt allow to update a service when the name is too long', function (): void {
        $service = Service::factory()->for($this->exhibition)->create(['name' => 'Zebra']);

        $page = visit('/login');
        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($this->route)
            ->assertSee($service->name)
            ->click('@edit-service-' . $service->id)
            ->assertSee('Название')
            ->clear('name')
            ->fill('name', generateTextWithChars(205))
            ->submit()
            ->assertSee('Название не должно превышать 200 символов');
    });

    it('doesnt allow to update a service when the description is too short', function (): void {
        $service = Service::factory()->for($this->exhibition)->create(['name' => 'Zebra']);

        $page = visit('/login');
        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($this->route)
            ->assertSee($service->name)
            ->click('@edit-service-' . $service->id)
            ->assertSee('Название')
            ->clear('description')
            ->fill('description', generateTextWithChars(3))
            ->submit()
            ->assertSee('Описание должно содержать не менее 10 символов');
    });

    it('doesnt allow to update a service when the description is too long', function (): void {
        $service = Service::factory()->for($this->exhibition)->create(['name' => 'Zebra']);

        $page = visit('/login');
        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($this->route)
            ->assertSee($service->name)
            ->click('@edit-service-' . $service->id)
            ->assertSee('Название')
            ->clear('description')
            ->fill('description', generateTextWithChars(5005))
            ->submit()
            ->assertSee('Описание не должно превышать 5000 символов');
    });

    it('doesnt allow to update a service when the placeholder is too short', function (): void {
        $service = Service::factory()->for($this->exhibition)->create(['name' => 'Zebra']);

        $page = visit('/login');
        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($this->route)
            ->assertSee($service->name)
            ->click('@edit-service-' . $service->id)
            ->assertSee('Название')
            ->clear('placeholder')
            ->fill('placeholder', generateTextWithChars(3))
            ->submit()
            ->assertSee('Подсказка должна содержать не менее 10 символов');
    });

    it('doesnt allow to update a service when the placeholder is too long', function (): void {
        $service = Service::factory()->for($this->exhibition)->create(['name' => 'Zebra']);

        $page = visit('/login');
        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($this->route)
            ->assertSee($service->name)
            ->click('@edit-service-' . $service->id)
            ->assertSee('Название')
            ->clear('placeholder')
            ->fill('placeholder', generateTextWithChars(5005))
            ->submit()
            ->assertSee('Длина подсказки не должна превышать 5000 символов');
    });

    it('allows to update a service with valid data', function (): void {
        $service = Service::factory()->for($this->exhibition)->create(['name' => 'Zebra']);

        $page = visit('/login');
        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $newName = 'new name of the service';
        $newDescription = 'new long description of the service';

        $page->navigate($this->route)
            ->assertSee($service->name)
            ->click('@edit-service-' . $service->id)
            ->assertSee('Название')
            ->clear('name')
            ->fill('name', $newName)
            ->clear('description')
            ->fill('description', $newDescription)
            ->clear('placeholder')
            ->fill('placeholder', generateTextWithChars(100))
            ->submit();

        $service = $service->fresh();
        $this->assertEquals($service->name, $newName);
        $this->assertEquals($service->description, $newDescription);
    });

    it('allows to delete a service', function (): void {
        $service = Service::factory()->for($this->exhibition)->create(['name' => 'Zebra']);

        $page = visit('/login');
        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($this->route)
            ->assertSee($service->name)
            ->click('@edit-service-' . $service->id)
            ->assertSee('Название')
            ->click('@delete-service')
            ->click('@delete-btn')
            ->assertPathEndsWith($this->route)
            ->assertDontSee($service->name);
    });

    it('allows only super admin and admin with exhibition access to see the services index page', function (): void {
        $adminWithAccess = User::factory()->create();
        $adminWithAccess->assignRole(UserRole::ADMIN);
        $this->exhibition->users()->attach($adminWithAccess->id);
        $adminWithAccess->setActiveExhibition($this->exhibition->id);

        $adminWithoutAccess = User::factory()->create();
        $adminWithoutAccess->assignRole(UserRole::ADMIN);
        $newExhibition = Exhibition::factory()->create();
        $newExhibition->users()->attach($adminWithoutAccess->id);
        $adminWithoutAccess->setActiveExhibition($newExhibition->id);

        $exponent = User::factory()->create();
        $exponent->assignRole(UserRole::EXPONENT);

        $services = Service::factory(3)->for($this->exhibition)->create();

        $this->get($this->route)
            ->assertRedirect(route('login'));

        $this->actingAs($adminWithAccess)
            ->get($this->route)
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->component('admin/Services/Index')
                    ->has('services.data', 3)
            );

        \Pest\Laravel\actingAs($adminWithoutAccess)
            ->get($this->route)
            ->assertOk()
            ->assertInertia(
                fn($page) => $page->has('services.data', 0)
            );

        \Pest\Laravel\actingAs($exponent)
            ->get($this->route)
            ->assertForbidden();
    });
});
