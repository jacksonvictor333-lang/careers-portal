<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\Vacancy;
use App\Services\ApplicationStatusUpdater;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/** Staff: kubadilisha hali ya ombi na taarifa za usaili (moja, au wengi kwa pamoja) */
class ApplicationController extends Controller
{
    private const TYPES = ['Written', 'Practical', 'Oral'];

    public function edit(int $id)
    {
        $app = JobApplication::with(['applicant', 'vacancy', 'statusLogs.staff'])->findOrFail($id);

        return view('staff.applications.edit', [
            'app'      => $app,
            'statuses' => JobApplication::STATUSES,
            'types'    => self::TYPES,
            'comments' => \Illuminate\Support\Facades\Schema::hasTable('staff_reasons')
                ? \Illuminate\Support\Facades\DB::table('staff_reasons')->where('type', 'shortlist_comment')->where('is_active', true)->orderBy('name')->pluck('name')->all()
                : [],
        ]);
    }

    public function update(Request $request, int $id, ApplicationStatusUpdater $updater)
    {
        $app = JobApplication::findOrFail($id);

        $data = $request->validate([
            'status'           => ['required', Rule::in(JobApplication::STATUSES)],
            'status_reason'    => ['nullable', 'string', 'max:200'],
            'interview_type'   => [Rule::requiredIf(in_array($request->input('status'), ['Shortlisted', 'Interview', 'Oral Interview'], true)),
                                   'nullable', Rule::in(self::TYPES)],
            'interview_number' => ['nullable', 'string', 'max:50', Rule::unique('job_applications', 'interview_number')->ignore($app->id)],
            'interview_at'     => ['nullable', 'date'],
            'interview_time'   => ['nullable', 'date_format:H:i'],
            'interview_venue'  => ['nullable', 'string', 'max:500'],
            'oral_at'          => ['nullable', 'date'],
            'oral_time'        => ['nullable', 'date_format:H:i'],
            'oral_venue'       => ['nullable', 'string', 'max:500'],
        ], [
            'interview_type.required' => 'Choose the interview type (Written, Practical or Oral) for this status.',
        ]);

        $updater->apply($app, $data, Auth::guard('staff')->user());

        return redirect()->route('staff.applicants.show', $app->applicant_id)->with('success', 'Application updated.');
    }

    /** Badilisha hali ya maombi mengi ya tangazo moja kwa pamoja (mf. kushortlisti kikundi) */
    public function bulk(Request $request, int $vacancyId, ApplicationStatusUpdater $updater)
    {
        $vacancy = Vacancy::findOrFail($vacancyId);

        $data = $request->validate([
            'ids'             => ['required', 'array', 'min:1'],
            'ids.*'           => ['integer'],
            'status'          => ['required', Rule::in(JobApplication::STATUSES)],
            'status_reason'   => ['nullable', 'string', 'max:200'],
            'interview_type'  => [Rule::requiredIf(in_array($request->input('status'), ['Shortlisted', 'Interview', 'Oral Interview'], true)),
                                  'nullable', Rule::in(self::TYPES)],
            'interview_at'    => ['nullable', 'date'],
            'interview_time'  => ['nullable', 'date_format:H:i'],
            'interview_venue' => ['nullable', 'string', 'max:500'],
        ], [
            'ids.required'            => 'Tick at least one application.',
            'interview_type.required' => 'Choose the interview type (Written, Practical or Oral) for this status.',
        ]);

        // Zinazotumwa tu zenye thamani: sehemu tupu hazibadilishi data iliyopo
        $values = array_filter(
            collect($data)->only(['status', 'status_reason', 'interview_type', 'interview_at', 'interview_time', 'interview_venue'])->all(),
            fn ($v) => $v !== null && $v !== ''
        );

        $staff = Auth::guard('staff')->user();
        $apps  = $vacancy->applications()->whereIn('id', $data['ids'])->get();

        foreach ($apps as $app) {
            $updater->apply($app, $values, $staff);
        }

        return back()->with('success', $apps->count() . ' application(s) updated to "' . $data['status'] . '".');
    }
}
