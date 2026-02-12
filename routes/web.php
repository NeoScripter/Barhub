<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ExhibitionController as AdminExhibitionController;
use App\Http\Controllers\User\ExhibitionController as UserExhibitionController;
use App\Http\Controllers\Exponent\DashboardController as ExponentDashboardController;
use App\Http\Controllers\User\HomeController;
use App\Models\Exhibition;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::prefix('/exhibitions')
    ->name('exhibitions.')
    ->group(function (): void {
        Route::resource('', UserExhibitionController::class)->only(['index']);
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

        Route::resource('/exhibitions', AdminExhibitionController::class)
            ->middleware(['can:viewAny,' . Exhibition::class])
            ->only('index');

        Route::prefix('exhibitions/{exhibition}')
            ->name('exhibitions.')
            ->middleware('can:view,exhibition')
            ->group(function (): void {

                Route::get('/', [AdminExhibitionController::class, 'edit'])
                    ->middleware(['can:update,' . Exhibition::class])
                    ->name('edit');

                /*
                |--------------------------------------------------------------------------
                | Models Belonging To Exhibition
                |--------------------------------------------------------------------------
                */

                // Route::resource('events', ExhibitionEventController::class);
                // Route::resource('speakers', ExhibitionSpeakerController::class);
                // Route::resource('tickets', ExhibitionTicketController::class);
                // Route::resource('partners', ExhibitionPartnerController::class);
            });
    });
