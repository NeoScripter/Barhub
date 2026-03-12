<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Exhibition;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

final class AdminController extends Controller
{
    public function index(Exhibition $exhibition)
    {
        $admins = $exhibition->users()->get();
        $users = User::query()->select(['email', 'id', 'name', 'last_login_at'])
            ->where('role', UserRole::USER)
            ->whereNotIn('id', $admins->pluck('id'))
            ->get();

        return Inertia::render('admin/Admins/Index', [
            'exhibition' => $exhibition,
            'admins' => $admins,
            'users' => $users,
        ]);
    }

    public function update(Exhibition $exhibition, int $id): RedirectResponse
    {
        $user = User::query()->find($id);
        $exhibition->users()->syncWithoutDetaching($user->id);
        $user->update(['role' => UserRole::ADMIN]);
        $user->save();

        return back();
    }

    public function destroy(Exhibition $exhibition, int $id): RedirectResponse
    {
        $user = User::query()->find($id);
        $exhibition->users()->detach($user->id);

        if ($user->exhibitions()->doesntExist()) {
            $user->update(['role' => UserRole::USER]);
        }

        return back();
    }
}
