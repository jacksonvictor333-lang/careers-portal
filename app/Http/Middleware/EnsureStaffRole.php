<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ulinzi wa mfumo wa staff:
 *  - akaunti isiyotumika inatolewa nje mara moja
 *  - majukumu: staff.role:officer,admin  (admin anaruhusiwa kila mahali)
 *  - bila majukumu: mfanyakazi yeyote anayetumika (viewer, officer, admin)
 */
class EnsureStaffRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = Auth::guard('staff')->user();

        if (! $user || ! $user->is_active) {
            Auth::guard('staff')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('staff.login')
                ->withErrors(['credentials' => 'This account is disabled or you have been signed out.']);
        }

        if ($roles && $user->role !== 'admin' && ! in_array($user->role, $roles, true)) {
            abort(403, 'You do not have permission to do this.');
        }

        return $next($request);
    }
}
