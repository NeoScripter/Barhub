<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Exhibition;
use App\Models\User;
use Inertia\Inertia;

class ExponentController extends Controller
{
    public function index(Exhibition $exhibition, Company $company)
    {
        $exponents = $company->users()->get();
        $users = User::select(['email', 'id', 'name', 'last_login_at'])
            ->where('role', UserRole::USER)->get();

        return Inertia::render('admin/Exponents/Index', [
            'exhibition' => $exhibition,
            'company' => $company,
            'exponents' => $exponents,
            'users' => $users,
        ]);
    }

    public function update(Exhibition $exhibition, Company $company, int $id)
    {
        $user = User::find($id);
        $company->users()->save($user);
        $user->update(['role' => UserRole::EXPONENT]);
        $user->save();

        return redirect()->back();
    }
}
