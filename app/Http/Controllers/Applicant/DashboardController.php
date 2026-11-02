<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /** Hali zinazomaanisha mwombaji amepita hatua ya kwanza (shortlisted au zaidi) */
    private const SHORTLISTED = ['Shortlisted', 'Interview', 'Oral Interview', 'Selected'];

    public function __invoke()
    {
        $applicant = Auth::guard('applicant')->user();

        // Swali moja dogo: hesabu zote nne zinahesabiwa kutoka kwenye maombi ya mwombaji huyu
        $apps = JobApplication::query()
            ->where('applicant_id', $applicant->id)
            ->get(['status', 'interview_at', 'oral_at']);

        // "Attended" = tarehe imefika au imepita (tarehe ya baadaye ni usaili ulioandaliwa, bado haujafanyika)
        $today    = now('Africa/Dar_es_Salaam')->startOfDay();
        $attended = fn ($date) => $date !== null && $date->copy()->startOfDay()->lte($today);

        $stats = [
            'applications' => $apps->count(),
            'shortlisted'  => $apps->filter(fn ($a) => in_array($a->status, self::SHORTLISTED, true))->count(),
            'interviews'   => $apps->filter(fn ($a) => $attended($a->interview_at))->count(),
            'orals'        => $apps->filter(fn ($a) => $attended($a->oral_at))->count(),
        ];

        [$completeness, $checklist] = $applicant->profileCompleteness();

        $hour     = (int) now('Africa/Dar_es_Salaam')->format('G');
        $greeting = $hour < 12 ? 'Good Morning' : ($hour < 18 ? 'Good Afternoon' : 'Good Evening');

        return view('applicant.dashboard', compact('applicant', 'stats', 'completeness', 'checklist', 'greeting'));
    }
}
