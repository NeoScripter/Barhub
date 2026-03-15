<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Exhibition;
use App\Models\Service;
use App\Models\User;

describe('Admin Service Test', function (): void {

    it('renders the service index page', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();
        $services = Service::factory(3)->for($company)->create();
        $route = "/admin/companies/{$company->id}/services";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route);
        $page->assertSee($company->public_name);
        $page->assertSee($services[0]->name);
    });

    it('doesnt allow to create a service when the name is too long', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();
        $route = "/admin/companies/{$company->id}/services";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee($company->public_name)
            ->click('@create-service')
            ->assertSee('Название')
            ->fill('name', generateTextWithChars(204))
            ->fill('description', generateTextWithChars(5004))
            ->fill('placeholder', generateTextWithChars(50))
            ->submit()
            ->assertSee('Название не должно превышать 200 символов');
    });

    it('doesnt allow to create a service when the description is too short', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();
        $route = "/admin/companies/{$company->id}/services";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee($company->public_name)
            ->click('@create-service')
            ->assertSee('Название')
            ->fill('name', generateTextWithChars(50))
            ->fill('description', generateTextWithChars(3))
            ->fill('placeholder', generateTextWithChars(40))
            ->submit()
            ->assertSee('Описание должно содержать не менее 10 символов');
    });

    it('doesnt allow to create a service when the description is too long', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();
        $route = "/admin/companies/{$company->id}/services";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee($company->public_name)
            ->click('@create-service')
            ->assertSee('Название')
            ->fill('name', generateTextWithChars(50))
            ->fill('description', generateTextWithChars(5004))
            ->fill('placeholder', generateTextWithChars(40))
            ->submit()
            ->assertSee('Описание не должно превышать 5000 символов');
    });

    it('doesnt allow to create a service when the placeholder is too short', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();
        $route = "/admin/companies/{$company->id}/services";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee($company->public_name)
            ->click('@create-service')
            ->assertSee('Название')
            ->fill('name', generateTextWithChars(50))
            ->fill('description', generateTextWithChars(20))
            ->fill('placeholder', generateTextWithChars(3))
            ->submit()
            ->assertSee('Подсказка должна содержать не менее 10 символов');
    });
    it('doesnt allow to create a service when the placeholder is too long', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();
        $route = "/admin/companies/{$company->id}/services";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee($company->public_name)
            ->click('@create-service')
            ->assertSee('Название')
            ->fill('name', generateTextWithChars(50))
            ->fill('description', generateTextWithChars(100))
            ->fill('placeholder', generateTextWithChars(5005))
            ->submit()
            ->assertSee('Длина подсказки не должна превышать 5000 символов');
    });

    it('allows to create a service with valid data', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();
        $route = "/admin/companies/{$company->id}/services";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee($company->public_name)
            ->click('@create-service')
            ->assertSee('Название')
            ->fill('name', generateTextWithChars(50))
            ->fill('description', generateTextWithChars(20))
            ->fill('placeholder', generateTextWithChars(100))
            ->submit()
            ->assertPathEndsWith($route);
    });

    it('doesnt allow to update a service when the name is too long', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();
        $service = Service::factory()
            ->for($company)
            ->create(['name' => 'Zebra']);
        $route = "/admin/companies/{$company->id}/services";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee($company->public_name)
            ->assertSee($service->name)
            ->click('@edit-service-'.$service->id)
            ->assertSee('Название')
            ->clear('name')
            ->fill('name', generateTextWithChars(205))
            ->submit()
            ->assertSee('Название не должно превышать 200 символов');
    });

    it('doesnt allow to update a service when the description is too short', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();
        $service = Service::factory()
            ->for($company)
            ->create(['name' => 'Zebra']);
        $route = "/admin/companies/{$company->id}/services";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee($company->public_name)
            ->assertSee($service->name)
            ->click('@edit-service-'.$service->id)
            ->assertSee('Название')
            ->clear('description')
            ->fill('description', generateTextWithChars(3))
            ->submit()
            ->assertSee('Описание должно содержать не менее 10 символов');
    });

    it('doesnt allow to update a service when the description is too long', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();
        $service = Service::factory()
            ->for($company)
            ->create(['name' => 'Zebra']);
        $route = "/admin/companies/{$company->id}/services";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee($company->public_name)
            ->assertSee($service->name)
            ->click('@edit-service-'.$service->id)
            ->assertSee('Название')
            ->clear('description')
            ->fill('description', generateTextWithChars(5005))
            ->submit()
            ->assertSee('Описание не должно превышать 5000 символов');
    });

    it('doesnt allow to update a service when the placeholder is too short', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();
        $service = Service::factory()
            ->for($company)
            ->create(['name' => 'Zebra']);
        $route = "/admin/companies/{$company->id}/services";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee($company->public_name)
            ->assertSee($service->name)
            ->click('@edit-service-'.$service->id)
            ->assertSee('Название')
            ->clear('placeholder')
            ->fill('placeholder', generateTextWithChars(3))
            ->submit()
            ->assertSee('Подсказка должна содержать не менее 10 символов');
    });

    it('doesnt allow to update a service when the placeholder is too long', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();
        $service = Service::factory()
            ->for($company)
            ->create(['name' => 'Zebra']);
        $route = "/admin/companies/{$company->id}/services";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee($company->public_name)
            ->assertSee($service->name)
            ->click('@edit-service-'.$service->id)
            ->assertSee('Название')
            ->clear('placeholder')
            ->fill('placeholder', generateTextWithChars(5005))
            ->submit()
            ->assertSee('Длина подсказки не должна превышать 5000 символов');
    });

    it('allows to update a service with valid data', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();
        $service = Service::factory()
            ->for($company)
            ->create(['name' => 'Zebra']);
        $route = "/admin/companies/{$company->id}/services";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $newname = 'new name of the service';
        $newDescription = 'new long description of the service';

        $page->navigate($route)
            ->assertSee($company->public_name)
            ->assertSee($service->name)
            ->click('@edit-service-'.$service->id)
            ->assertSee('Название')
            ->clear('name')
            ->fill('name', $newname)
            ->clear('description')
            ->fill('description', $newDescription)
            ->clear('placeholder')
            ->fill('placeholder', generateTextWithChars(100))
            ->submit();

        $service = $service->fresh();
        $this->assertEquals($service->name, $newname);
        $this->assertEquals($service->description, $newDescription);
    });

    it('allows to delete a service', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();
        $service = Service::factory()
            ->for($company)
            ->create(['name' => 'Zebra']);
        $route = "/admin/companies/{$company->id}/services";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee($company->public_name)
            ->assertSee($service->name)
            ->click('@edit-service-'.$service->id)
            ->assertSee('Название')
            ->click('@delete-service')
            ->click('@delete-btn')
            ->assertPathEndsWith($route)
            ->assertDontSee($service->name);
    });

    it('allows only super admin and admin with exhibition access to see the services index page', function (): void {
        $adminWithAccess = User::factory()->create([
            'email' => 'admin1@gmail.com',
            'password' => 'password',
        ]);
        $adminWithoutAccess = User::factory()->create([
            'email' => 'admin2@gmail.com',
            'password' => 'password',
        ]);
        $exponent = User::factory()->create([
            'email' => 'exponent@gmail.com',
            'password' => 'password',
        ]);
        $adminWithAccess->assignRole(UserRole::ADMIN);
        $adminWithoutAccess->assignRole(UserRole::ADMIN);
        $exponent->assignRole(UserRole::EXPONENT);
        $exhibitionWithCompany = Exhibition::factory()->create();
        $exhibitionWithoutCompany = Exhibition::factory()->create();
        $exhibitionWithCompany->users()->attach($adminWithAccess->id);
        $exhibitionWithoutCompany->users()->attach($adminWithoutAccess->id);
        $company = Company::factory()->for($exhibitionWithCompany)->create();
        $services = Service::factory(3)->for($company)->create();
        $route = "/admin/companies/{$company->id}/services";

        visit($route)->assertSee('Вход в аккаунт');

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'admin1@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('admin1@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route);
        $page->assertSee($company->public_name);
        $page->assertSee($services[0]->name);

        $page->click('@logout-dropdown');
        $page->assertSee('Выйти');
        $page->click('@logout-button');

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'admin2@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('admin2@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)->assertSee('Unauthorized');
        $page->navigate('/admin/dashboard');
        $page->click('@logout-dropdown');
        $page->assertSee('Выйти');
        $page->click('@logout-button');

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'exponent@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('exponent@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)->assertSee('Unauthorized');
    });
});
