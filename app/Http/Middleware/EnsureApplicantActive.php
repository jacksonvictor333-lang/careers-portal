<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * - Akaunti iliyofungiwa (Locked): mwombaji anatolewa nje.
 * - Akaunti isiyowashwa (Not Active): anaweza kuingia na kuona Dashboard tu, ikiwa na ujumbe
 *   "Your account is not activated"; sehemu zote zinaonekana hafifu na hakuna anachoweza kuendelea nacho
 *   mpaka admin amuwashe.
 */
class EnsureApplicantActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('applicant')->user();

        if ($user && ($user->is_locked ?? false)) {
            Auth::guard('applicant')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('applicant.login')
                ->withErrors(['email' => 'Your account is locked. Please contact the Employment Secretariat.']);
        }

        if ($user && $user->is_active === false) {
            View::share('apInactive', true);

            if (! $request->routeIs('applicant.dashboard', 'applicant.logout')) {
                return redirect()->route('applicant.dashboard');
            }
        }

        return $next($request);
    }
}
