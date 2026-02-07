<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExhibitionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn() => Inertia::render('Home'))->name('home');

// Route::get('dashboard', function () {
//     return Inertia::render('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// require __DIR__.'/settings.php';

Route::prefix('/admin')
    ->name('admin.')
    ->middleware([
        'auth',
        'role:' . UserRole::ADMIN->value . ',' . UserRole::SUPER_ADMIN->value,
    ])
    ->group(function (): void {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');

        Route::resource('/exhibitions', ExhibitionController::class)
            ->middleware(['role:' . UserRole::SUPER_ADMIN->value])
            ->only('index');

        /*
        |--------------------------------------------------------------------------
        | Single Exhibition Context
        |--------------------------------------------------------------------------
        */

        Route::prefix('exhibitions/{exhibition}')
            ->name('exhibitions.')
            // ->middleware('permission:' . UserPermission::VIEW_EXHIBITIONS->value)
            ->group(function (): void {

                Route::get('/', [ExhibitionController::class, 'show'])
                    ->name('show');

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
