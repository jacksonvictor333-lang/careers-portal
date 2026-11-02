<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\StaffUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('staff')->check()) {
            return redirect()->route('staff.dashboard');
        }

        return view('staff.auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = StaffUser::where('email', strtolower(trim($data['email'])))->first();

        // Ujumbe mmoja kwa kosa lolote (email/nenosiri) ili kutofichua kama akaunti ipo
        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return back()->withInput($request->only('email'))
                ->withErrors(['credentials' => 'The email or password is incorrect.']);
        }

        if (! $user->is_active) {
            return back()->withInput($request->only('email'))
                ->withErrors(['credentials' => 'This account is disabled. Contact the administrator.']);
        }

        Auth::guard('staff')->login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        $user->forceFill(['last_login_at' => now()])->saveQuietly();

        return redirect()->intended(route('staff.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::guard('staff')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('staff.login');
    }
}
