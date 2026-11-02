<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CvPreviewController extends Controller
{
    public function __invoke()
    {
        $applicant = Auth::guard('applicant')->user()->load([
            'academicQualifications',
            'professionalQualifications',
            'languageProficiencies',
            'workExperiences',
            'trainings',
            'computerSkills',
            'referees',
            'attachments',
        ]);

        return view('applicant.cv-preview', compact('applicant'));
    }
}
