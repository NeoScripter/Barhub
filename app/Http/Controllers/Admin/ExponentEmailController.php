<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\ExponentEmail;
use App\Notifications\CreateExponentNotification;
use App\Notifications\RegisterExponentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class ExponentEmailController extends Controller
{
    public function store(Request $request, Company $company)
    {
        $validated = $request->validate([
            'email' => ['required', 'min:3', 'email', 'unique:exponent_emails,email']
        ]);

        $email = ExponentEmail::create([
            'email' => $validated['email'],
            'company_id' => $company->id,
        ]);

        Notification::route('mail', $email->email)
            ->notify(new RegisterExponentNotification($email->email, $company->public_name, $company->exhibition->name));

        return to_route('admin.exponents.index', ['company' => $company]);
    }

    public function destroy(Company $company, ExponentEmail $email)
    {
        $email->delete();

        return to_route('admin.exponents.index', ['company' => $company]);
    }
}
