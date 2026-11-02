<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('applicant')->check()) {
            return redirect()->route('applicant.dashboard');
        }

        return view('applicant.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Kikomo: majaribio 5 kwa dakika kwa kila email + IP
        $key = Str::lower($credentials['email']) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        $ok = Auth::guard('applicant')->attempt(
            $credentials + ['is_active' => true],
            $request->boolean('remember')
        );

        if (! $ok) {
            RateLimiter::hit($key, 60);

            // Ujumbe mmoja kwa makosa yote: haufichui kama email ipo
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();

        return redirect()->intended(route('applicant.dashboard'));
    }

    public function logout(Request $request)
    {
        // Tunamtoa applicant tu (hatuharibu session nzima)
        Auth::guard('applicant')->logout();
        $request->session()->regenerateToken();

        return redirect()->route('applicant.login');
    }
}