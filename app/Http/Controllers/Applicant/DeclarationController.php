<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeclarationController extends Controller
{
    public function show()
    {
        return view('applicant.declarations', [
            'applicant' => Auth::guard('applicant')->user(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'declaration_name' => ['required', 'string', 'max:150'],
            'agree'            => ['accepted'],
        ], [
            'agree.accepted' => 'You must confirm the declaration to continue.',
        ]);

        Auth::guard('applicant')->user()->update([
            'declaration_name'        => $data['declaration_name'],
            'declaration_accepted_at' => now(),
        ]);

        return redirect()->route('applicant.declaration')->with('success', 'Declaration saved.');
    }
}
