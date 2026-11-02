<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/** Kila mfanyakazi: kuona akaunti yake na kubadilisha password */
class AccountController extends Controller
{
    public function show()
    {
        return view('staff.account', ['me' => Auth::guard('staff')->user()]);
    }

    public function updateProfile(Request $request)
    {
        $me = Auth::guard('staff')->user();

        $data = $request->validate([
            'first_name'  => ['required', 'string', 'max:80'],
            'middle_name' => ['nullable', 'string', 'max:80'],
            'last_name'   => ['required', 'string', 'max:80'],
            'phone'       => ['nullable', 'string', 'regex:/^\+?[0-9]{9,15}$/'],
        ], ['phone.regex' => 'Enter a valid mobile number, e.g. +255712345678.']);

        $me->forceFill($data)->save();   // jina kamili linatungwa kiotomatiki

        return back()->with('success', 'Profile updated.');
    }

    public function updatePassword(Request $request)
    {
        $me = Auth::guard('staff')->user();

        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'confirmed', 'different:current_password', Password::min(8)->letters()->numbers()],
        ]);

        if (! Hash::check($request->input('current_password'), $me->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $me->forceFill(['password' => $request->input('password')])->save();

        return back()->with('success', 'Password changed.');
    }
}
