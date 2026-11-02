<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\Vacancy;

/** Reports: muhtasari wa maombi kwa tangazo na kupakua orodha (CSV, inafunguka kwenye Excel) */
class ReportController extends Controller
{
    public function index()
    {
        $vacancies = Vacancy::query()->withCount('applications')->orderByDesc('id')->get();

        $byStatus = JobApplication::query()
            ->selectRaw('vacancy_id, status, count(*) as total')
            ->groupBy('vacancy_id', 'status')->get()
            ->groupBy('vacancy_id')
            ->map(fn ($rows) => $rows->pluck('total', 'status')->all());

        return view('staff.reports.index', [
            'vacancies' => $vacancies,
            'byStatus'  => $byStatus,
            'statuses'  => JobApplication::STATUSES,
            'totals'    => JobApplication::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status')->all(),
        ]);
    }

    public function csv(int $id)
    {
        $vacancy = Vacancy::findOrFail($id);
        $apps = $vacancy->applications()->with('applicant')->orderBy('id')->get();

        $name = 'applications-' . preg_replace('/[^A-Za-z0-9_-]+/', '-', $vacancy->reference_number) . '.csv';

        return response()->streamDownload(function () use ($apps) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM ili Excel isome herufi vizuri
            fputcsv($out, ['SN', 'Application no.', 'Name', 'Email', 'Phone', 'Gender', 'Applied on', 'Status', 'Interview type', 'Interview no.', 'Interview date', 'Time', 'Venue']);
            foreach ($apps as $i => $a) {
                $p = $a->applicant;
                fputcsv($out, [
                    $i + 1, optional($p)->application_number, optional($p)->full_name, optional($p)->email, optional($p)->phone, optional($p)->gender,
                    optional($a->applied_at)->format('Y-m-d'), $a->status, $a->interview_type, $a->interview_number,
                    optional($a->interview_at)->format('Y-m-d'), $a->interview_time ? substr((string) $a->interview_time, 0, 5) : '', $a->interview_venue,
                ]);
            }
            fclose($out);
        }, $name, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
