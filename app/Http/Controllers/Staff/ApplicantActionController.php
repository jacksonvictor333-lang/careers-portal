<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\ApplicantRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;

/** Vitendo vya menyu ya "Action" kwenye orodha ya waombaji */
class ApplicantActionController extends Controller
{
    /** Tuma kiungo cha kuweka nenosiri jipya kwa email ya mwombaji */
    public function sendResetLink(int $id)
    {
        $a = Applicant::findOrFail($id);

        $url = URL::temporarySignedRoute('applicant.password.reset', now()->addMinutes(60), ['id' => $a->id]);

        try {
            Mail::raw(
                "Hello {$a->full_name},\n\nUse the link below to set a new password for your Careers Portal account. "
                . "The link works for 60 minutes.\n\n{$url}\n\nIf you did not expect this message, ignore it.",
                fn ($m) => $m->to($a->email)->subject('Reset your Careers Portal password')
            );
            $msg = 'Reset link sent to ' . $a->email . '.';
        } catch (\Throwable $e) {
            report($e);
            $msg = 'The email could not be sent. Check the mail settings in .env.';
        }

        // Kwenye mazingira ya local (mail = log) onyesha kiungo ili kujaribu
        if (app()->environment('local')) {
            $msg .= ' [Local test link: ' . $url . ']';
        }

        return back()->with('success', $msg);
    }

    /** Thibitisha ulemavu aliojaza mwombaji */
    public function confirmDisability(int $id)
    {
        $a = Applicant::findOrFail($id);

        if (strcasecmp((string) $a->disability, 'Yes') !== 0) {
            return back()->with('error', 'This applicant has not declared a disability.');
        }

        $a->forceFill([
            'disability_confirmed_at' => now(),
            'disability_confirmed_by' => Auth::guard('staff')->id(),
        ])->save();

        return back()->with('success', 'Disability confirmed for ' . $a->full_name . '.');
    }

    /** Washa au zima akaunti */
    public function toggleActive(int $id)
    {
        $a = Applicant::findOrFail($id);
        $a->forceFill(['is_active' => ! $a->is_active])->save();

        // Maombi ya activation yaliyokuwa yanasubiri yanafungwa
        if ($a->is_active) {
            ApplicantRequest::where('applicant_id', $a->id)->where('type', 'activate')->where('status', 'pending')
                ->update(['status' => 'approved', 'decided_by' => Auth::guard('staff')->id(), 'decided_at' => now()]);
        }

        return back()->with('success', $a->full_name . ($a->is_active ? ' is now active.' : ' has been deactivated.'));
    }

    /** Funga au fungua akaunti (Locked) */
    public function toggleLock(int $id)
    {
        $a = Applicant::findOrFail($id);
        $a->forceFill(['is_locked' => ! $a->is_locked])->save();

        if (! $a->is_locked) {
            ApplicantRequest::where('applicant_id', $a->id)->where('type', 'unlock')->where('status', 'pending')
                ->update(['status' => 'approved', 'decided_by' => Auth::guard('staff')->id(), 'decided_at' => now()]);
        }

        return back()->with('success', $a->full_name . ($a->is_locked ? ' is now locked.' : ' has been unlocked.'));
    }

    /** Omba activation (au unlock ikiwa amefungiwa); admin ndiye anaamua */
    public function requestActivation(Request $request, int $id)
    {
        $a    = Applicant::findOrFail($id);
        $type = $a->is_locked ? 'unlock' : 'activate';

        if ($type === 'activate' && $a->is_active) {
            return back()->with('error', 'This applicant is already active.');
        }

        $exists = ApplicantRequest::where('applicant_id', $a->id)->where('type', $type)->where('status', 'pending')->exists();
        if ($exists) {
            return back()->with('error', 'A request for this applicant is already waiting for approval.');
        }

        ApplicantRequest::create([
            'applicant_id' => $a->id,
            'type'         => $type,
            'reason'       => $request->input('reason') ? mb_substr((string) $request->input('reason'), 0, 300) : null,
            'requested_by' => Auth::guard('staff')->id(),
        ]);

        return back()->with('success', ucfirst($type) . ' request sent. An admin will review it.');
    }

    /** Admin: kubali au kataa ombi */
    public function decide(Request $request, int $requestId)
    {
        $req = ApplicantRequest::with('applicant')->where('status', 'pending')->findOrFail($requestId);
        $approve = $request->input('decision') === 'approve';

        if ($approve) {
            $req->applicant->forceFill($req->type === 'unlock' ? ['is_locked' => false] : ['is_active' => true])->save();
        }

        $req->forceFill([
            'status' => $approve ? 'approved' : 'rejected', 'decided_by' => Auth::guard('staff')->id(), 'decided_at' => now(),
        ])->save();

        return back()->with('success', 'Request ' . ($approve ? 'approved.' : 'rejected.'));
    }

    // ---------- Admin: password ya muda (OTP) na kubadilisha email ----------

    /** Tengeneza password ya muda yenyewe, iwekwe kwa mwombaji, na ionyeshwe juu ya ukurasa (mara moja) */
    public function generatePassword(int $id)
    {
        $a = Applicant::findOrFail($id);

        $letters = 'ABCDEFGHJKMNPQRSTUVWXYZ';
        $digits  = '23456789';
        $pool    = $letters . $digits;
        $pwd     = $letters[random_int(0, strlen($letters) - 1)] . $digits[random_int(0, strlen($digits) - 1)];
        for ($i = 0; $i < 6; $i++) {
            $pwd .= $pool[random_int(0, strlen($pool) - 1)];
        }
        $pwd = str_shuffle($pwd);

        $a->forceFill(['password' => $pwd])->save();

        return back()->with('otp', ['name' => trim((string) $a->full_name), 'email' => $a->email, 'password' => $pwd]);
    }

    /** Admin: futa cheti/faili la mwombaji (faili linafutwa kabisa kwenye hifadhi) */
    public function deleteFile(Request $request, int $id)
    {
        $a = Applicant::findOrFail($id);

        $data = $request->validate([
            'kind' => ['required', Rule::in(['professional', 'training', 'attachment'])],
            'item' => ['required', 'integer'],
        ]);

        $map = [
            'professional'         => [\App\Models\ProfessionalQualification::class, 'attachment_path'],
            'training'             => [\App\Models\Training::class, 'attachment_path'],
            'attachment'           => [\App\Models\Attachment::class, 'attachment_path'],
        ];
        [$model, $col] = $map[$data['kind']];

        $item = $model::where('applicant_id', $a->id)->findOrFail($data['item']);
        $path = $item->{$col};

        if ($path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
        }

        if ($data['kind'] === 'attachment') {
            $item->delete();                 // kiambatisho kinahitaji faili, kwa hiyo rekodi inaondolewa
        } else {
            $item->{$col} = null;
            $item->save();
        }

        return back()->with('success', 'The file was deleted.');
    }

    /** Admin: futa kiwango kimoja cha elimu (pamoja na cheti, transcript na matoleo ya zamani ya faili) */
    public function deleteAcademic(int $id, int $qid)
    {
        $a = Applicant::findOrFail($id);
        $q = \App\Models\AcademicQualification::where('applicant_id', $a->id)->findOrFail($qid);
        $disk = \Illuminate\Support\Facades\Storage::disk('public');

        // Matoleo ya zamani ya faili: yafutwe na yawekewe alama "Removed" kwenye Document History
        $current = array_filter([$q->attachment_path, $q->transcript_path]);
        $old = \App\Models\DocumentHistory::where('academic_qualification_id', $q->id)->where('action', 'uploaded')
            ->pluck('path')->unique()->reject(fn ($p) => in_array($p, $current, true));
        foreach ($old as $path) {
            $disk->delete($path);
            \App\Models\DocumentHistory::create(['applicant_id' => $a->id, 'academic_qualification_id' => $q->id,
                'level' => $q->level, 'doc_type' => 'Old file', 'path' => $path, 'action' => 'removed']);
        }
        foreach ($current as $path) {
            $disk->delete($path);   // rekodi ya "removed" inaandikwa na model wakati wa kufuta
        }

        $level = $q->level;
        $q->delete();

        return back()->with('success', $level . ' was deleted from this applicant.');
    }

    public function updateEmail(Request $request, int $id)
    {
        $a = Applicant::findOrFail($id);
        $data = $request->validate([
            'email' => ['required', 'email', 'max:150', 'confirmed', Rule::unique('applicants', 'email')->ignore($a->id)],
        ], [
            'email.confirmed' => 'The two email addresses do not match.',
            'email.unique'    => 'That email is already used by another applicant.',
        ]);
        $a->forceFill(['email' => strtolower($data['email'])])->save();

        return back()->with('success', 'Email changed for ' . ($a->full_name ?: $a->email) . '.');
    }
}
