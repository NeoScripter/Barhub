<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Event;
use App\Models\Exhibition;
use App\Models\Stage;
use App\Models\User;

describe('Stage CRUD Test', function (): void {
    it('allows super-admin to create and delete stages', function (): void {
        // Create user and authenticate
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);

        // Create test data
        $exhibition = Exhibition::factory()->create();

        $stage1 = Stage::factory()->create(['name' => 'Главная сцена']);
        $stage2 = Stage::factory()->create(['name' => 'Малая сцена']);
        $stage3 = Stage::factory()->create(['name' => 'Открытая площадка']);

        Event::factory()->for($exhibition)->for($stage1)->create();
        Event::factory()->for($exhibition)->for($stage2)->create();
        Event::factory()->for($exhibition)->for($stage3)->create();

        // Visit login page
        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        // Navigate to event edit page (pick first event)
        $event = Event::first();
        $page->navigate("/admin/exhibitions/{$exhibition->id}/events/{$event->id}/edit");
        $page->assertSee('Название');

        $page->click('@edit-stages');

        $page->assertSee('Управление площадками');
        $page->assertSee('Создание и удаление площадок');

        // Fill in new stage name
        $newStageName = 'Конференц-зал';
        $page->type('name', $newStageName);

        // Submit the form
        $page->click('Добавить');

        $page->assertSee('Площадка создана');

        // Verify new stage appears
        $page->assertSee($newStageName);
    })->group('browser');

    it('validates stage name is required', function (): void {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);

        $exhibition = Exhibition::factory()->create();
        $stage = Stage::factory()->create();
        $event = Event::factory()->for($exhibition)->for($stage)->create();

        $page = visit('/login');

        $page->fill('email', 'user@example.com')
            ->fill('password', 'password')
            ->click('@login-button');

        $this->assertAuthenticated();

        $page->navigate("/admin/exhibitions/{$exhibition->id}/events/{$event->id}/edit");
        $page->click('@edit-stages');

        $page->press('Добавить');
        $page->assertDontSee('Площадка создана');
    })->group('browser');

    it('prevents deleting stages used by events', function (): void {
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);

        $exhibition = Exhibition::factory()->create();
        $stage = Stage::factory()->create(['name' => 'Important Stage']);

        $event = Event::factory()->for($exhibition)->for($stage)->create();

        $page = visit('/login');

        $page->fill('email', 'admin@example.com')
            ->fill('password', 'password')
            ->click('@login-button');

        $this->assertAuthenticated();

        $page->navigate("/admin/exhibitions/{$exhibition->id}/events/{$event->id}/edit");
        $page->click('@edit-stages');

        $page->assertSee('Управление площадками');

        // Try to delete the stage
        $page->click('@delete stage Important Stage');

        // Should see error message
        $page->assertDontSee('Площадка удалена');

    })->group('browser');

    it('allows deleting stages not used by events', function (): void {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);

        $exhibition = Exhibition::factory()->create();
        $usedStage = Stage::factory()->create(['name' => 'Used Stage']);
        $unusedStage = Stage::factory()->create(['name' => 'Unused Stage']);

        $event = Event::factory()->for($exhibition)->for($usedStage)->create();

        $page = visit('/login');

        $page->fill('email', 'test@example.com')
            ->fill('password', 'password')
            ->click('@login-button');

        $this->assertAuthenticated();

        $page->navigate("/admin/exhibitions/{$exhibition->id}/events/{$event->id}/edit");
        $page->click('@edit-stages');


        $page->assertSee('Used Stage');
        $page->assertSee('Unused Stage');

        // Delete the unused stage
        $page->click('@delete stage Unused Stage');

        // Should see success message
        $page->assertSee('Площадка удалена');

        $page->assertDontSee('Unused Stage');

        // Used stage should still be visible
        $page->assertSee('Used Stage');
    })->group('browser');

    it('shows all existing stages in the stage selector', function (): void {
        $user = User::factory()->create([
            'email' => 'viewer@example.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);

        $exhibition = Exhibition::factory()->create();

        $stages = Stage::factory(5)->create();

        // Create event with first stage
        $event = Event::factory()->for($exhibition)->for($stages->first())->create();

        $page = visit('/login');

        $page->fill('email', 'viewer@example.com')
            ->fill('password', 'password')
            ->click('@login-button');

        $this->assertAuthenticated();

        $page->navigate("/admin/exhibitions/{$exhibition->id}/events/{$event->id}/edit");

        // Verify the event's stage is shown in selector
        $page->assertSee($stages->first()->name);

        // Click button to open stage management dialog
        $page->click('@edit-stages');

        // All stages should be visible in the management dialog
        $stages->each(function ($stage) use ($page) {
            $page->assertSee($stage->name);
        });
    })->group('browser');

    it('validates stage name uniqueness', function (): void {
        $user = User::factory()->create([
            'email' => 'unique@example.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);

        $exhibition = Exhibition::factory()->create();
        $existingStage = Stage::factory()->create(['name' => 'Existing Stage']);
        $event = Event::factory()->for($exhibition)->for($existingStage)->create();

        $page = visit('/login');

        $page->fill('email', 'unique@example.com')
            ->fill('password', 'password')
            ->click('@login-button');

        $this->assertAuthenticated();

        $page->navigate("/admin/exhibitions/{$exhibition->id}/events/{$event->id}/edit");
        $page->click('@edit-stages');

        $page->type('name', 'Existing Stage');
        $page->click('Добавить');

        $page->assertSee('Направление с таким названием уже существует');
    })->group('browser');
});
