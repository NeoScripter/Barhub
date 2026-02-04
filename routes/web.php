<?php

use App\Enums\UserPermission;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExhibitionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');

// Route::get('dashboard', function () {
//     return Inertia::render('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// require __DIR__.'/settings.php';

Route::prefix('/admin')
    ->name('admin.')
    ->middleware([
        'auth',
        'permission:' . UserPermission::ACCESS_ADMIN_PANEL->value
    ])
    ->group(function () {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');
        Route::resource('/exhibitions', ExhibitionController::class)->only(['index']);
    });
