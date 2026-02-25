<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\ExhibitionController as AdminExhibitionController;
use App\Http\Controllers\Admin\PersonController as AdminPersonController;
use App\Http\Controllers\Admin\StageController;
use App\Http\Controllers\Admin\ThemeController;
use App\Http\Controllers\Admin\UpdatedExhibitionStatusController;
use App\Http\Controllers\Exponent\DashboardController as ExponentDashboardController;
use App\Http\Controllers\User\EventController as UserEventController;
use App\Http\Controllers\User\ExhibitionController as UserExhibitionController;
use App\Http\Controllers\User\HomeController;
use App\Models\Exhibition;
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
    });

Route::prefix('/admin')
    ->name('admin.')
    ->middleware([
        'auth',
        'role:' . UserRole::ADMIN->value . ',' . UserRole::SUPER_ADMIN->value,
    ])
    ->group(function (): void {
        Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');

        Route::patch('exhibitions/{exhibition}/status', UpdatedExhibitionStatusController::class)
            ->name('update.status');

        Route::resource('/exhibitions', AdminExhibitionController::class)
            ->middleware(['can:viewAny,' . Exhibition::class])
            ->only(['index', 'update']);

        Route::resource('themes', ThemeController::class)->only(['store', 'destroy']);
        Route::resource('stages', StageController::class)->only(['store', 'destroy']);

        Route::prefix('exhibitions/{exhibition}')
            ->name('exhibitions.')
            ->middleware('can:view,exhibition')
            ->group(function (): void {

                Route::get('/edit', (new AdminExhibitionController())->edit(...))
                    ->middleware(['can:update,' . Exhibition::class])
                    ->name('edit');

                Route::get('/', (new AdminExhibitionController())->show(...))
                    ->name('show');

                Route::resource('events', AdminEventController::class)->except('show');
                Route::resource('people', AdminPersonController::class)->only(['index', 'edit']);
                /*
                |--------------------------------------------------------------------------
                | Models Belonging To Exhibition
                |--------------------------------------------------------------------------
                */

                // Route::resource('speakers', ExhibitionSpeakerController::class);
                // Route::resource('tickets', ExhibitionTicketController::class);
                // Route::resource('partners', ExhibitionPartnerController::class);
            });
    });
