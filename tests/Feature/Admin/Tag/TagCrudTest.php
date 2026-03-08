<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Exhibition;
use App\Models\Tag;
use App\Models\User;

describe('Tag CRUD Test', function (): void {

    it('allows super-admin to create and delete tags', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);

        $this->user = $user;
        $this->exhibition = Exhibition::factory()->create();
        $this->company = Company::factory()->for($this->exhibition)->create();

        $tag1 = Tag::factory()->create(['name' => 'Выставка']);
        $tag2 = Tag::factory()->create(['name' => 'Турнир']);
        $tag3 = Tag::factory()->create(['name' => 'Лекция']);

        $this->company->tags()->attach([$tag1->id, $tag2->id, $tag3->id]);
        $this->route = "/admin/exhibitions/{$this->exhibition->id}/companies/{$this->company->id}/edit";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        // Navigate to event edit page
        $page->navigate($this->route);

        $page->assertSee('Управление тегами');

        $page->click('@edit-tags');

        $page->assertSee('Добавить тег');

        $page->press('Добавить');
        $page->assertCount('#tag-list li', 3);

        $newTagName = 'Семинар';
        $page->type('tagname', $newTagName);

        // Submit the form
        $page->click('Добавить');

        $page->assertSee($newTagName);
        $page->assertCount('#tag-list li', 4);
        $page->click('@delete tag '.$newTagName);

        $page->assertCount('#tag-list li', 3);
        $page->assertDontSee($newTagName);
    })->group('browser');
});
