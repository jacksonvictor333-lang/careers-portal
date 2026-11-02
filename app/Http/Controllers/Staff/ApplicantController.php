<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\ApplicantRequest;
use App\Models\ApplicationStatusLog;
use App\Models\DocumentHistory;
use App\Models\JobApplication;
use App\Models\Vacancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/** Staff: orodha ya waombaji (tabs), wasifu wa kila mmoja, faili na barua */
class ApplicantController extends Controller
{
    private const TABS = ['list', 'pending', 'locked', 'requests', 'nida'];

    public function index(Request $request)
    {
        $tab = in_array($request->query('tab'), self::TABS, true) ? $request->query('tab') : 'list';
        $q   = trim((string) $request->query('q'));
        $per = in_array((int) $request->query('per'), [10, 25, 50, 100], true) ? (int) $request->query('per') : 10;

        $counts = [
            'pending'  => Applicant::where('is_active', false)->where('is_locked', false)->count(),
            'locked'   => Applicant::where('is_locked', true)->count(),
            'requests' => ApplicantRequest::where('status', 'pending')->count(),
        ];

        $search = fn ($query) => $this->applySearch($query, $q);

        if ($tab === 'requests') {
            $requests = ApplicantRequest::with(['applicant', 'requester'])
                ->when($q !== '', fn ($qq) => $qq->whereHas('applicant', fn ($a) => $search($a)))
                ->orderByRaw("status = 'pending' desc")->orderByDesc('id')
                ->paginate($per)->appends($request->except('ajax'));

            return $this->respond($request, compact('tab', 'q', 'per', 'counts', 'requests'));
        }

        $applicants = $search(Applicant::query()->withCount(['jobApplications', 'referees'])->withExists(['academicQualifications', 'professionalQualifications', 'languageProficiencies', 'workExperiences', 'trainings', 'computerSkills']))
            ->when($tab === 'pending', fn ($qq) => $qq->where('is_active', false)->where('is_locked', false))
            ->when($tab === 'locked', fn ($qq) => $qq->where('is_locked', true))
            ->when($tab === 'nida', fn ($qq) => $qq->whereNotNull('nida_number'))
            ->when($q !== '' && ! preg_match('/[@0-9]/', $q), fn ($qq) => $qq->orderByRaw('CASE WHEN first_name LIKE ? THEN 0 ELSE 1 END', [$this->firstToken($q) . '%']))
            ->orderByDesc('id')
            ->paginate($per)->appends($request->except('ajax'));

        return $this->respond($request, compact('tab', 'q', 'per', 'counts', 'applicants'));
    }

    /** Wasifu (Applicant CV): taarifa za mwombaji tu; maombi ya kazi yako kwenye "Applications" */
    /** ajax=1 -> kipande cha jedwali tu (kwa utafutaji wa moja kwa moja); vinginevyo ukurasa mzima */
    private function respond(Request $request, array $data)
    {
        if ($request->ajax() || $request->boolean('ajax')) {
            return response(view('staff.applicants._results', $data)->render());
        }

        return view('staff.applicants.index', $data);
    }

    private function firstToken(string $q): string
    {
        $t = preg_split('/\s+/', strtolower(trim($q)))[0] ?? '';

        return preg_replace('/[^a-z]/', '', $t);
    }

    /**
     * Utafutaji wa jina unaoanza kuvuta kwa herufi ya kwanza:
     *  - "a"        -> majina yoyote (la kwanza/kati/la mwisho) yanayoanza na A
     *  - "ann mo"   -> la kwanza "ann..." na la mwisho (au la kati) "mo..."
     *  - "J V V" au "jvv" -> herufi za mwanzo za majina matatu (first, middle, last)
     *  - barua pepe, simu au namba ya maombi (ikiwa na @ au tarakimu) -> inatafutwa popote
     */
    private function applySearch($query, string $q)
    {
        $q = trim(preg_replace('/\s+/', ' ', $q));
        if ($q === '') {
            return $query;
        }

        if (preg_match('/[@0-9]/', $q)) {
            return $query->where(fn ($w) => $w->where('email', 'like', "%{$q}%")->orWhere('phone', 'like', "%{$q}%")->orWhere('application_number', 'like', "%{$q}%"));
        }

        $tokens = array_values(array_filter(array_map(fn ($t) => preg_replace('/[^a-z]/', '', $t), explode(' ', strtolower($q)))));
        if (! $tokens) {
            return $query->whereRaw('1 = 0');
        }

        $sets = [$tokens];
        if (count($tokens) === 1 && preg_match('/^[a-z]{2,3}$/', $tokens[0])) {
            $sets[] = str_split($tokens[0]);          // "jvv" = J V V
        }

        $map = [
            1 => [['first_name'], ['middle_name'], ['last_name']],
            2 => [['first_name', 'middle_name'], ['first_name', 'last_name'], ['middle_name', 'last_name']],
            3 => [['first_name', 'middle_name', 'last_name']],
        ];

        return $query->where(function ($w) use ($sets, $map, $tokens) {
            foreach ($sets as $set) {
                foreach ($map[count($set)] ?? [] as $cols) {
                    $w->orWhere(function ($x) use ($set, $cols) {
                        foreach ($cols as $i => $c) {
                            $x->where($c, 'like', $set[$i] . '%');
                        }
                    });
                }
            }
            // Waombaji ambao bado hawajajaza majina: tumia mwanzo wa barua pepe
            $w->orWhere(fn ($z) => $z->whereNull('first_name')->whereNull('last_name')->where('email', 'like', implode('%', $tokens) . '%'));
        });
    }

    public function show(int $id)
    {
        $applicant = Applicant::with([
            'academicQualifications', 'professionalQualifications', 'languageProficiencies',
            'workExperiences', 'trainings', 'computerSkills', 'referees', 'attachments',
        ])->withCount('jobApplications')->findOrFail($id);

        return view('staff.applicants.show', ['applicant' => $applicant, 'docHistory' => $this->documentHistory($applicant)]);
    }

    /**
     * Document History kwa kila kiwango: kila cheti/transcript alichopakia (cha kwanza, cha pili...),
     * kipi ni cha sasa (Current), kipi kilibadilishwa (Replaced) au kuondolewa (Removed).
     */
    private function documentHistory(Applicant $applicant): array
    {
        $rows = DocumentHistory::where('applicant_id', $applicant->id)->where('action', 'uploaded')->orderBy('id')->get();
        $current = [];
        foreach ($applicant->academicQualifications as $q) {
            foreach (\App\Models\AcademicQualification::DOC_FIELDS as $field => $type) {
                if ($q->{$field}) { $current[$q->id][$type] = $q->{$field}; }
            }
        }
        $removed = DocumentHistory::where('applicant_id', $applicant->id)->where('action', 'removed')->pluck('path')->all();

        // Vyeti vilivyokuwepo kabla ya historia kuwashwa: onyesha kama upload ya kwanza
        $known = $rows->pluck('path')->all();
        $out = [];
        foreach ($applicant->academicQualifications as $q) {
            foreach (\App\Models\AcademicQualification::DOC_FIELDS as $field => $type) {
                if ($q->{$field} && ! in_array($q->{$field}, $known, true)) {
                    $rows->push(new DocumentHistory(['academic_qualification_id' => $q->id, 'level' => $q->level, 'doc_type' => $type, 'path' => $q->{$field}, 'action' => 'uploaded', 'created_at' => $q->created_at]));
                }
            }
        }

        $groups = $rows->groupBy(fn ($r) => ($r->level ?: 'Other') . '|' . $r->academic_qualification_id . '|' . $r->doc_type);
        foreach ($groups as $key => $items) {
            [$level, $qid, $type] = explode('|', $key);
            $items = $items->values();
            foreach ($items as $i => $r) {
                $isCurrent = isset($current[(int) $qid][$type]) && $current[(int) $qid][$type] === $r->path;
                $out[$level][] = [
                    'type'    => $type, 'n' => $i + 1, 'path' => $r->path, 'at' => $r->created_at,
                    'status'  => $isCurrent ? 'Current' : (in_array($r->path, $removed, true) ? 'Removed' : 'Replaced'),
                ];
            }
        }
        ksort($out);

        return $out;
    }

    /** Maombi ya kazi aliyotuma mwombaji */
    public function applications(int $id)
    {
        $applicant = Applicant::withCount('jobApplications')->findOrFail($id);
        $apps = $applicant->jobApplications()->with(['vacancy', 'letter'])->orderByDesc('id')->get();

        return view('staff.applicants.applications', compact('applicant', 'apps'));
    }

    /** Historia ya akaunti: usajili, maombi, mabadiliko ya hali, requests za activation/unlock */
    public function history(int $id)
    {
        $applicant = Applicant::withCount('jobApplications')->findOrFail($id);
        $events = collect();

        $events->push(['at' => $applicant->created_at, 'icon' => 'bi-person-plus', 'title' => 'Account created', 'note' => $applicant->email, 'by' => null]);

        if ($applicant->declaration_accepted_at) {
            $events->push(['at' => $applicant->declaration_accepted_at, 'icon' => 'bi-patch-check', 'title' => 'Declaration accepted', 'note' => $applicant->declaration_name, 'by' => null]);
        }
        if ($applicant->disability_confirmed_at) {
            $events->push(['at' => $applicant->disability_confirmed_at, 'icon' => 'bi-person-check', 'title' => 'Disability confirmed', 'note' => null,
                'by' => optional(\App\Models\StaffUser::find($applicant->disability_confirmed_by))->name]);
        }

        foreach ($applicant->jobApplications()->get() as $a) {
            $events->push(['at' => $a->applied_at ?: $a->created_at, 'icon' => 'bi-send', 'title' => 'Applied: ' . $a->vacancy_title, 'note' => $a->reference_number, 'by' => null]);
        }

        ApplicationStatusLog::with('staff', 'jobApplication')
            ->whereIn('job_application_id', $applicant->jobApplications()->pluck('id'))->get()
            ->each(fn ($l) => $events->push(['at' => $l->created_at, 'icon' => 'bi-arrow-repeat',
                'title' => 'Status: ' . ($l->from_status ?: '—') . ' → ' . $l->to_status . ' (' . optional($l->jobApplication)->vacancy_title . ')',
                'note' => $l->note, 'by' => optional($l->staff)->name]));

        ApplicantRequest::with(['requester', 'decider'])->where('applicant_id', $applicant->id)->get()
            ->each(function ($r) use ($events) {
                $events->push(['at' => $r->created_at, 'icon' => 'bi-hand-index-thumb', 'title' => $r->type_label . ' requested', 'note' => $r->reason, 'by' => optional($r->requester)->name]);
                if ($r->decided_at) {
                    $events->push(['at' => $r->decided_at, 'icon' => 'bi-clipboard-check', 'title' => $r->type_label . ' request ' . $r->status, 'note' => null, 'by' => optional($r->decider)->name]);
                }
            });

        $events = $events->filter(fn ($e) => $e['at'])->sortByDesc('at')->values();

        return view('staff.applicants.history', compact('applicant', 'events'));
    }

    /** Application Check: je, mwombaji anakidhi sifa za tangazo lililochaguliwa? */
    public function check(Request $request, int $id)
    {
        $applicant = Applicant::withCount('jobApplications')->findOrFail($id);
        $vacancies = Vacancy::orderByDesc('id')->get(['id', 'title', 'reference_number', 'employer']);
        $vacancy   = $request->filled('vacancy') ? Vacancy::find($request->query('vacancy')) : null;
        $result    = $vacancy ? $vacancy->eligibility($applicant) : null;

        return view('staff.applicants.check', compact('applicant', 'vacancies', 'vacancy', 'result'));
    }

    /**
     * Faili za mwombaji (vyeti, transcripts, picha, barua). Njia lazima iwe ndani ya folda za waombaji
     * (applicants/..., letters/...) ili kuzuia kufikia faili nyingine.
     */
    public function file(Request $request)
    {
        $path = (string) $request->query('path');

        abort_if($path === '' || str_contains($path, '..') || ! preg_match('#^(applicants|letters)/#', $path), 404);
        abort_unless(Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->response($path);
    }

    /** Barua ya maombi ya ombi (PDF iliyopakiwa, au ukurasa wa barua) */
    public function letter(int $id)
    {
        $app    = JobApplication::with(['applicant', 'letter', 'vacancy'])->findOrFail($id);
        $letter = $app->letter;

        if ($letter && $letter->file_path && Storage::disk('public')->exists($letter->file_path)) {
            return Storage::disk('public')->response($letter->file_path);
        }

        abort_unless($letter, 404);

        return response()->view('applicant.application-letter', [
            'applicant' => $app->applicant,
            'app'       => $app,
            'letter'    => $letter,
            'asPdf'     => false,
        ]);
    }
}
