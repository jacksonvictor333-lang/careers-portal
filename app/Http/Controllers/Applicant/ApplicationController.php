<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\ApplicationLetter;
use App\Models\JobApplication;
use App\Models\Vacancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * Kuomba tangazo la kazi kwa kuambatisha barua (PDF), na kubadilisha barua hiyo
 * kabla tangazo halijafungwa.
 */
class ApplicationController extends Controller
{
    private function applicant()
    {
        return Auth::guard('applicant')->user();
    }

    // ---------- Kuomba ----------

    public function create(int $id)
    {
        $vacancy = Vacancy::published()->findOrFail($id);

        if ($blocked = $this->blockReason($vacancy, $this->applicant())) {
            return redirect()->route('vacancies.show', $vacancy->id)->with('error', $blocked);
        }

        return view('applicant.vacancies.apply', [
            'mode'    => 'create',
            'vacancy' => $vacancy,
            'current' => null,
            'action'  => route('applicant.vacancies.apply.store', $vacancy->id),
        ]);
    }

    public function store(Request $request, int $id)
    {
        $applicant = $this->applicant();
        $vacancy   = Vacancy::published()->findOrFail($id);

        if ($blocked = $this->blockReason($vacancy, $applicant)) {
            return redirect()->route('vacancies.show', $vacancy->id)->with('error', $blocked);
        }

        $this->validated($request, true, false);

        $app = DB::transaction(function () use ($request, $applicant, $vacancy) {
            // Kupitia uhusiano: applicant_id inawekwa yenyewe
            $app = $applicant->jobApplications()->create([
                'vacancy_id'       => $vacancy->id,
                'vacancy_title'    => $vacancy->title,
                'institution'      => $vacancy->employer,
                'reference_number' => $vacancy->reference_number,
                'status'           => 'Submitted',
                'applied_at'       => now('Africa/Dar_es_Salaam')->toDateString(),
            ]);

            $this->saveLetterFile($app, $request);

            return $app;
        });

        $applicant->touch();

        return redirect()->route('applicant.my-applications')
            ->with('success', 'Application received. Your application for "' . $app->vacancy_title . '" has been received. '
                . 'You can replace your letter until ' . $vacancy->closes_at->format('d F Y') . '.');
    }

    // ---------- Kubadilisha barua kabla tangazo halijafungwa ----------

    public function edit(int $id)
    {
        $app = $this->applicant()->jobApplications()->with(['vacancy', 'letter'])->findOrFail($id);

        if (! $app->can_edit) {
            return redirect()->route('applicant.my-applications')
                ->with('error', 'This application can no longer be edited (the advert is closed or the application has moved on).');
        }

        return view('applicant.vacancies.apply', [
            'mode'    => 'edit',
            'vacancy' => $app->vacancy,
            'current' => optional($app->letter)->is_uploaded ? $app->letter : null,
            'action'  => route('applicant.my-applications.letter.update', $app->id),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $applicant = $this->applicant();
        $app       = $applicant->jobApplications()->with(['vacancy', 'letter'])->findOrFail($id);

        if (! $app->can_edit) {
            return redirect()->route('applicant.my-applications')
                ->with('error', 'This application can no longer be edited (the advert is closed or the application has moved on).');
        }

        $this->validated($request, false, (bool) optional($app->letter)->is_uploaded);

        DB::transaction(fn () => $this->saveLetterFile($app, $request));
        $applicant->touch();

        return redirect()->route('applicant.my-applications')->with('success', 'Your application letter has been updated.');
    }

    // ---------- Msaada ----------

    /** Sababu ya kuzuia kuomba, au null ikiwa anaweza */
    private function blockReason(Vacancy $vacancy, $applicant): ?string
    {
        if (! $vacancy->is_open) {
            return 'This advert is closed. Applications are no longer accepted.';
        }

        if ($applicant->jobApplications()->where('vacancy_id', $vacancy->id)->exists()) {
            return 'You have already applied for this advert. You can replace your letter under My Applications.';
        }

        $eligibility = $vacancy->eligibility($applicant);
        if (! $eligibility['ok']) {
            // Sababu halisi ya kila sifa isiyotimizwa (mf. kiwango cha elimu), pamoja na kuelekeza kuangalia wasifu
            return collect($eligibility['checks'])->where('ok', false)->pluck('fix')->filter()->implode(' ')
                ?: 'Your profile does not meet the requirements of this advert.';
        }

        return null;
    }

    /** Barua (PDF) ni lazima wakati wa kuomba; wakati wa kubadilisha ni lazima tu kama bado hajapakia */
    private function validated(Request $request, bool $creating, bool $hasUploaded): array
    {
        $rules = [
            'letter_file' => [Rule::requiredIf($creating || ! $hasUploaded), 'nullable', 'file', 'mimes:pdf', 'max:2048'],
        ];

        // Uthibitisho wa anuani ya barua unahitajika wakati wa kuomba na wakati wa kubadilisha barua
        $rules['confirm'] = ['accepted'];

        return $request->validate($rules, [
            'letter_file.required' => 'Attach your letter (PDF).',
            'letter_file.mimes'    => 'Your letter must be a PDF file.',
            'letter_file.max'      => 'Your letter must not be larger than 2 MB.',
            'confirm.accepted'     => 'Please confirm that your letter is addressed as shown.',
        ]);
    }

    /**
     * Hifadhi PDF ya barua (storage/app/public/letters/{applicant_id}/...) na uandike njia yake
     * kwenye application_letters.file_path. Bila faili mpya, barua iliyopo inabaki.
     */
    private function saveLetterFile(JobApplication $app, Request $request): void
    {
        if (! $request->hasFile('letter_file')) {
            return;
        }

        $applicant = $this->applicant();

        // Kuunda ombi huunda barua ya mfano kiotomatiki; hapa tunaiwekea PDF ya mwombaji
        $existing = $app->letter()->first();

        if ($existing && $existing->file_path) {
            Storage::disk('public')->delete($existing->file_path);
        }

        $path = $request->file('letter_file')
            ->storeAs('letters/' . $applicant->id, 'uploaded-letter-' . $app->id . '.pdf', 'public');

        $default = ApplicationLetter::compose($app->vacancy_title, $app->institution);

        $app->letter()->updateOrCreate([], [
            'subject'     => $existing->subject ?? $default['subject'],
            'body'        => $existing->body ?? $default['body'],
            'file_path'   => $path,
            'is_uploaded' => true,
        ]);
    }
}
