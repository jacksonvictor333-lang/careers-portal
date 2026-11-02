<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show()
    {
        return view('applicant.profile', [
            'applicant' => Auth::guard('applicant')->user(),
        ]);
    }

    /** Badilisha email: inahitaji nenosiri la sasa */
    public function updateEmail(Request $request)
    {
        $applicant = Auth::guard('applicant')->user();

        $data = $request->validate([
            'new_email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('applicants', 'email')->ignore($applicant->id),
            ],
            'email_password' => ['required', 'current_password:applicant'],
        ], [
            'new_email.unique'                      => 'This email is already used by another account.',
            'email_password.current_password'       => 'The password you entered is not correct.',
            'email_password.required'               => 'Enter your current password to confirm.',
        ]);

        $applicant->update(['email' => $data['new_email']]);

        return redirect()->route('applicant.profile')->with('success', 'Your email has been updated.');
    }

    /** Badilisha nenosiri: inahitaji nenosiri la sasa */
    public function updatePassword(Request $request)
    {
        $applicant = Auth::guard('applicant')->user();

        $request->validate([
            'current_password' => ['required', 'current_password:applicant'],
            'password'         => ['required', 'confirmed', 'different:current_password', Password::min(8)->letters()->numbers()],
        ], [
            'current_password.current_password' => 'The current password is not correct.',
            'password.confirmed'                => 'The new password and its confirmation do not match.',
            'password.different'                => 'The new password must be different from the current one.',
        ]);

        $applicant->update(['password' => $request->input('password')]);   // inahifadhiwa kwa hash otomatiki

        $request->session()->regenerate();

        return redirect()->route('applicant.profile')->with('success', 'Your password has been changed.');
    }
}
