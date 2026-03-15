<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Exhibition;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

final class ExponentController extends Controller
{
    public function index(Company $company)
    {
        $exponents = $company->users()->get();
        $users = User::query()->select(['email', 'id', 'name', 'last_login_at'])
            ->where('role', UserRole::USER)
            ->whereNotIn('id', $exponents->pluck('id'))
            ->get();

        return Inertia::render('admin/Exponents/Index', [
            'company' => $company,
            'exponents' => $exponents,
            'users' => $users,
        ]);
    }

    public function update(Company $company, int $id): RedirectResponse
    {
        $user = User::query()->find($id);
        $company->users()->save($user);
        $user->update(['role' => UserRole::EXPONENT]);
        $user->save();

        return back();
    }

    public function destroy(Company $company, int $id): RedirectResponse
    {
        $user = User::query()->find($id);
        $user->update(['company_id' => null, 'role' => UserRole::USER]);
        $user->save();

        return back();
    }
}
