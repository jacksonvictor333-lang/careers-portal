<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MyApplicationsController extends Controller
{
    public function __invoke()
    {
        $applicant = Auth::guard('applicant')->user();

        return view('applicant.my-applications', [
            'applicant'    => $applicant,
            'applications' => $applicant->jobApplications()->with('vacancy')->get(),
        ]);
    }
}
