<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ExhibitionController;
use App\Http\Controllers\Exponent\DashboardController as ExponentDashboardController;
use App\Models\Exhibition;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn() => Inertia::render('Home'))->name('home');

// Route::get('dashboard', function () {
//     return Inertia::render('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('/exponent')
    ->name('exponent.')
    ->middleware([
        'auth',
        'role:' . UserRole::EXPONENT->value
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

        Route::resource('/exhibitions', ExhibitionController::class)
            ->middleware(['can:viewAny,' . Exhibition::class])
            ->only('index');

        Route::prefix('exhibitions/{exhibition}')
            ->name('exhibitions.')
            ->middleware('can:view,exhibition')
            ->group(function (): void {

                Route::get('/', [ExhibitionController::class, 'edit'])
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
