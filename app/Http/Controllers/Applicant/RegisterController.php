<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/** Kujisajili kwa mwombaji mpya (Create Account): email na nenosiri tu */
class RegisterController extends Controller
{
    public function show()
    {
        if (Auth::guard('applicant')->check()) {
            return redirect()->route('applicant.dashboard');
        }

        return view('applicant.auth.register');
    }

    public function store(Request $request)
    {
        if (Auth::guard('applicant')->check()) {
            return redirect()->route('applicant.dashboard');
        }

        $request->merge(['email' => strtolower(trim((string) $request->input('email')))]);

        $data = $request->validate([
            'email'    => ['required', 'email:rfc', 'max:255', Rule::unique('applicants', 'email')],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ], [
            'email.unique'       => 'An account with this email already exists. Please log in instead.',
            'password.confirmed' => 'The two passwords do not match. Please type the same password in both fields.',
            'password.min'       => 'The password must be at least 8 characters.',
        ]);

        // Majina na simu vinajazwa baadaye kwenye Personal Details.
        // Nambari ya maombi (application_number) inatengenezwa na model baada ya kuundwa.
        $applicant = Applicant::create([
            'email'     => $data['email'],
            'password'  => $data['password'],   // inafichwa (hashed) na model
            'is_active' => false,   // inawashwa na admin wa staff
        ]);

        Auth::guard('applicant')->login($applicant);
        $request->session()->regenerate();

        return redirect()->route('applicant.dashboard');
    }
}
