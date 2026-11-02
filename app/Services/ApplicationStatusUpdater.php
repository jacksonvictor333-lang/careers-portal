<?php

namespace App\Services;

use App\Models\ApplicationStatusLog;
use App\Models\JobApplication;
use App\Models\StaffUser;

/**
 * Inabadilisha hali ya ombi na taarifa za usaili (namba, aina, tarehe, saa, mahali) na kuhifadhi historia.
 * Inatumika na ukurasa wa ombi moja na kubadilisha maombi mengi kwa pamoja.
 */
class ApplicationStatusUpdater
{
    /** Hali ambazo zinahitaji namba ya usaili */
    public const NEEDS_NUMBER = ['Shortlisted', 'Interview', 'Oral Interview', 'Selected'];

    /** Sehemu zinazoruhusiwa kubadilishwa */
    public const FIELDS = [
        'status', 'status_reason', 'interview_type', 'interview_number',
        'interview_at', 'interview_time', 'interview_venue',
        'oral_at', 'oral_time', 'oral_venue',
    ];

    /**
     * Weka thamani zilizomo ndani ya $data tu (sehemu isiyomo haibadilishwi; thamani tupu "" inafuta).
     */
    public function apply(JobApplication $app, array $data, ?StaffUser $by = null): JobApplication
    {
        $oldStatus = $app->status;

        foreach (self::FIELDS as $field) {
            if (array_key_exists($field, $data)) {
                $value = $data[$field];
                $app->{$field} = ($value === '' || $value === null) ? null : $value;
            }
        }

        // Namba ya usaili hutolewa kiotomatiki mtu akifikia hatua inayoihitaji
        if (in_array($app->status, self::NEEDS_NUMBER, true) && blank($app->interview_number)) {
            $app->interview_number = $this->nextNumber($app);
        }

        $app->save();

        if ($app->status !== $oldStatus) {
            ApplicationStatusLog::create([
                'job_application_id' => $app->id,
                'staff_user_id'      => $by?->id,
                'from_status'        => $oldStatus,
                'to_status'          => $app->status,
                'note'               => $app->status_reason,
            ]);
        }

        return $app;
    }

    /** NITN/{mwaka}/{nambari ya tangazo, tarakimu 5}/{mfuatano, tarakimu 4} - isiyorudiwa */
    private function nextNumber(JobApplication $app): string
    {
        $post = str_pad((string) ($app->vacancy_id ?: $app->id), 5, '0', STR_PAD_LEFT);
        $seq  = JobApplication::query()
            ->where('vacancy_id', $app->vacancy_id)
            ->whereNotNull('interview_number')
            ->count() + 1;

        do {
            $number = JobApplication::makeInterviewNumber($post, $seq++);
        } while (JobApplication::where('interview_number', $number)->exists());

        return $number;
    }
}
