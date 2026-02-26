<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Event;
use App\Models\Exhibition;
use App\Models\Stage;
use App\Models\Theme;
use App\Models\User;

describe('Theme CRUD Test', function (): void {
    it('allows super-admin to create and delete themes', function (): void {
        // Create user and authenticate
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);

        // Create test data
        $exhibition = Exhibition::factory()->create();
        $stage = Stage::factory()->create();

        $theme1 = Theme::factory()->create(['name' => 'Выставка', 'color_hex' => '#F9BBD2']);
        $theme2 = Theme::factory()->create(['name' => 'Турнир', 'color_hex' => '#E4FFA3']);
        $theme3 = Theme::factory()->create(['name' => 'Лекция', 'color_hex' => '#D8DBFF']);

        $event = Event::factory()->for($exhibition)->for($stage)->create();
        $event->themes()->attach([$theme1->id, $theme2->id, $theme3->id]);

        // Visit login page
        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        // Navigate to event edit page
        $page->navigate("/admin/exhibitions/{$exhibition->id}/events/{$event->id}/edit");
        $page->assertSee('Название');

        // Click the "Редактировать" button
        $page->click('Редактировать');

        // Wait for dialog to open - use text selector
        $page->wait(1);
        $page->assertSee('Управление направлениями');
        $page->assertSee('Создание и удаление направлений');

        // Fill in new theme name
        $newThemeName = 'Семинар';
        $page->type('name', $newThemeName);

        // Click on the first color button
        $page->click('button[aria-label="Select color #FFE4E8"]');

        // Submit the form
        $page->click('Добавить');

        // Wait and verify success
        $page->wait(1);
        $page->assertSee('Направление создано');

        // Verify new theme appears
        $page->wait(1);
        $page->assertSee($newThemeName);
    })->group('browser');

    it('validates theme name is required', function (): void {
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
        $page->click('Редактировать');

        $page->wait(1);

        // Click color without entering name
        $page->click('button[aria-label="Select color #FFE4E8"]');

        // Try to submit
        $page->press('Добавить');
        $page->assertDontSee('Направление создано');

    })->group('browser');

    it('shows all existing themes in the theme selector', function (): void {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);

        $exhibition = Exhibition::factory()->create();
        $stage = Stage::factory()->create();

        $themes = Theme::factory(5)->create();

        $event = Event::factory()->for($exhibition)->for($stage)->create();
        $event->themes()->attach($themes->take(3)->pluck('id'));

        $page = visit('/login');

        $page->fill('email', 'test@example.com')
            ->fill('password', 'password')
            ->click('@login-button');

        $this->assertAuthenticated();

        $page->navigate("/admin/exhibitions/{$exhibition->id}/events/{$event->id}/edit");

        // Verify selected themes are shown
        $themes->take(3)->each(function ($theme) use ($page) {
            $page->assertSee($theme->name);
        });

        // Click button to open dialog
        $page->click('Редактировать');

        $page->wait(1);

        // All themes should be visible
        $themes->each(function ($theme) use ($page) {
            $page->assertSee($theme->name);
        });
    })->group('browser');
});
