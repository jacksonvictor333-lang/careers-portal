<?php

namespace App\Http\Controllers;

use App\Models\Vacancy;
use Illuminate\Support\Facades\Auth;

/** Matangazo ya kazi: orodha na maelezo (wazi kwa wageni pia) */
class VacancyController extends Controller
{
    public function index()
    {
        $applicant = Auth::guard('applicant')->user();

        // Yaliyo wazi kwanza (yanayofungwa mapema juu), yaliyofungwa mwishoni
        $vacancies = Vacancy::published()->get()
            ->sortBy(fn (Vacancy $v) => [$v->is_open ? 0 : 1, $v->closes_at->timestamp])
            ->values();

        $applied = $applicant
            ? $applicant->jobApplications()->whereNotNull('vacancy_id')->pluck('id', 'vacancy_id')
            : collect();

        return view('applicant.vacancies.index', compact('vacancies', 'applied', 'applicant'));
    }

    public function show(int $id)
    {
        $applicant = Auth::guard('applicant')->user();
        $vacancy   = Vacancy::published()->findOrFail($id);

        $eligibility = $applicant ? $vacancy->eligibility($applicant) : null;
        $existing    = $applicant ? $applicant->jobApplications()->where('vacancy_id', $vacancy->id)->first() : null;

        return view('applicant.vacancies.show', compact('vacancy', 'applicant', 'eligibility', 'existing'));
    }
}
