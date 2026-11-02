<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\JobApplication;
use App\Models\Vacancy;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $today = Vacancy::today();

        $stats = [
            'open'         => Vacancy::where('is_published', true)->where('closes_at', '>=', $today)->count(),
            'closed'       => Vacancy::where(fn ($q) => $q->where('is_published', false)->orWhere('closes_at', '<', $today))->count(),
            'applicants'   => Applicant::count(),
            'applications' => JobApplication::count(),
            'pending'      => Applicant::where('is_active', false)->where('is_locked', false)->count(),
        ];

        $recent = JobApplication::with('applicant')->orderByDesc('id')->limit(8)->get();

        return view('staff.dashboard', compact('stats', 'recent'));
    }
}
