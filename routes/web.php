<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CompanyController as AdminCompanyController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\ExhibitionController as AdminExhibitionController;
use App\Http\Controllers\Admin\ExponentController as AdminExponentController;
use App\Http\Controllers\Admin\FollowupController as AdminFollowupController;
use App\Http\Controllers\Admin\InfoItemController;
use App\Http\Controllers\Admin\LinkController;
use App\Http\Controllers\Admin\PartnerController as AdminPartnerController;
use App\Http\Controllers\Admin\PersonController as AdminPersonController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\StageController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\TaskController as AdminTaskController;
use App\Http\Controllers\Admin\TaskTemplateController;
use App\Http\Controllers\Admin\ThemeController;
use App\Http\Controllers\Exponent\FollowupController as ExponentFollowupController;
use App\Http\Controllers\Exponent\InfoItemController as ExponentInfoItemController;
use App\Http\Controllers\Exponent\CompanyController as ExponentCompanyController;
use App\Http\Controllers\Exponent\DashboardController as ExponentDashboardController;
use App\Http\Controllers\User\EventController as UserEventController;
use App\Http\Controllers\User\ExhibitionController as UserExhibitionController;
use App\Http\Controllers\User\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/exhibitions', UserExhibitionController::class)->name('exhibitions.index');

Route::prefix('/exhibitions/{exhibition}')
    ->group(function (): void {
        Route::resource('events', UserEventController::class)->only(['index', 'show']);
    });

Route::prefix('/exponent')
    ->name('exponent.')
    ->middleware([
        'auth',
        'role:' . UserRole::EXPONENT->value,
    ])
    ->group(function (): void {
        Route::get('/dashboard', ExponentDashboardController::class)->name('dashboard');
        Route::resource('followups', ExponentFollowupController::class)->only(['index', 'store']);
        Route::resource('info-items', ExponentInfoItemController::class)->only(['index']);
        Route::resource('companies', ExponentCompanyController::class)->only(['index', 'edit', 'update']);
    });

Route::prefix('/admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function (): void {
        Route::resource('dashboard', AdminDashboardController::class)->only(['index', 'update']);
        Route::get('/links', LinkController::class)->name('public_links');

        Route::resource('exhibitions', AdminExhibitionController::class)
            ->middleware(['role:' . UserRole::SUPER_ADMIN->value])
            ->except('show');

        Route::resource('themes', ThemeController::class)->only(['store', 'destroy']);
        Route::resource('stages', StageController::class)->only(['store', 'destroy']);
        Route::resource('tags', TagController::class)->only(['store', 'destroy']);

        Route::resource('exhibitions/{exhibition}/admins', AdminController::class)
            ->middleware(['role:' . UserRole::SUPER_ADMIN->value])
            ->only(['index', 'destroy', 'update']);

        Route::resource('all-tasks', AdminPartnerController::class)
            ->only(['index', 'edit', 'update']);
        Route::resource('followups', AdminFollowupController::class)
            ->only(['index', 'edit', 'update']);
        Route::resource('companies/{company}/exponents', AdminExponentController::class)
            ->only(['update', 'index', 'destroy']);
        Route::resource('companies/{company}/tasks', AdminTaskController::class);
        Route::resource('task-templates', TaskTemplateController::class);
        Route::resource('info-items', InfoItemController::class);
        Route::resource('companies/{company}/services', AdminServiceController::class);

        Route::resource('events', AdminEventController::class)->except('show');
        Route::resource('people', AdminPersonController::class)->except(['show']);
        Route::resource('companies', AdminCompanyController::class)->except(['show']);
    });
