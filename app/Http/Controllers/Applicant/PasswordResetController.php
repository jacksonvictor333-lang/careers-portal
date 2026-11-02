<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

/** Ukurasa wa kuweka nenosiri jipya kupitia kiungo kilichotumwa na staff (signed URL, dakika 60) */
class PasswordResetController extends Controller
{
    public function show(Request $request, int $id)
    {
        $applicant = Applicant::findOrFail($id);

        return view('applicant.auth.reset-password', ['applicant' => $applicant, 'action' => $request->fullUrl()]);
    }

    public function update(Request $request, int $id)
    {
        $applicant = Applicant::findOrFail($id);

        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $applicant->forceFill(['password' => $request->input('password')])->save();

        return redirect()->route('applicant.login')->with('status', 'Your password has been changed. You can now sign in.');
    }
}
