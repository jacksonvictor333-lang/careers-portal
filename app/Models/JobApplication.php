<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    public const STATUSES = [
        'Submitted',
        'Shortlisted',
        'Interview',
        'Oral Interview',
        'Selected',
        'Not Selected',
    ];

    /** Kiambishi cha namba ya usaili, mfano NITN/26/12464/0015 */
    public const INTERVIEW_PREFIX = 'NITN';

    /** Aina za usaili: mwombaji anaambiwa ataanza na ipi */
    public const INTERVIEW_TYPES = ['Written', 'Practical', 'Oral'];

    protected $guarded = ['id', 'applicant_id'];

    protected function casts(): array
    {
        return [
            'applied_at'   => 'date',
            'interview_at' => 'date',
            'oral_at'      => 'date',
        ];
    }

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }

    public function vacancy()
    {
        return $this->belongsTo(Vacancy::class);
    }

    /** Viambatisho vilivyoambatishwa kwenye ombi hili */
    public function attachments()
    {
        return $this->belongsToMany(Attachment::class, 'application_attachments', 'job_application_id', 'attachment_id')->withTimestamps();
    }

    /** Barua na viambatisho vinahaririwa tu kabla tangazo halijafungwa, na ombi likiwa bado "Submitted" */
    public function getCanEditAttribute(): bool
    {
        return $this->status === 'Submitted' && $this->vacancy && $this->vacancy->is_open;
    }

    /** Historia ya mabadiliko ya hali (kutoka mfumo wa staff) */
    public function statusLogs()
    {
        return $this->hasMany(ApplicationStatusLog::class)->orderByDesc('id');
    }

    /** Barua ya maombi ya ombi hili (kutoka table `application_letters`) */
    public function letter()
    {
        return $this->hasOne(ApplicationLetter::class);
    }

    /** Ombi jipya likiundwa, barua yake ya mfano inawekwa kiotomatiki (isipowekwa na mfumo mwingine) */
    protected static function booted(): void
    {
        static::created(function (self $app) {
            if (! $app->letter()->exists()) {
                ApplicationLetter::createDefaultFor($app);
            }
        });
    }

    /** Darasa la rangi ya hali (kwa badge kwenye jedwali) */
    public function getStatusClassAttribute(): string
    {
        return match ($this->status) {
            'Shortlisted'    => 'st-blue',
            'Interview'      => 'st-amber',
            'Oral Interview' => 'st-purple',
            'Selected'       => 'st-green',
            'Not Selected'   => 'st-red',
            default          => 'st-gray',
        };
    }

    /** Jina linaloonekana kwa mwombaji ("Submitted" huonekana kama "Application Received") */
    public function getStatusLabelAttribute(): string
    {
        return $this->status === 'Submitted' ? 'Application Received' : (string) $this->status;
    }

    /** Aikoni ya hali (Bootstrap Icons) */
    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            'Shortlisted'    => 'bi-list-check',
            'Interview'      => 'bi-person-check',
            'Oral Interview' => 'bi-mic',
            'Selected'       => 'bi-trophy',
            'Not Selected'   => 'bi-x-circle',
            default          => 'bi-send',
        };
    }

    /**
     * Sababu fupi ya hali (safu ya "Status reason"):
     *  - maelezo mafupi yaliyowekwa kwenye `status_reason` yakiwepo, vinginevyo
     *  - neno fupi kulingana na hali (mf. "Shortlisted"); "Submitted" haina sababu => null ("N/A").
     */
    public function getStatusMessageAttribute(): ?string
    {
        if (filled($this->status_reason)) {
            return $this->status_reason;
        }

        return match ($this->status) {
            'Shortlisted'    => 'Shortlisted',
            'Interview'      => 'Interview stage',
            'Oral Interview' => 'Oral interview stage',
            'Selected'       => 'Selected',
            'Not Selected'   => 'Not selected',
            default          => null,
        };
    }

    /** Aina ya usaili chini ya sababu: "Written interview", "Practical interview", "Oral interview" */
    public function getInterviewTypeLabelAttribute(): ?string
    {
        return filled($this->interview_type) ? $this->interview_type . ' interview' : null;
    }

    /**
     * Tengeneza namba ya usaili: NITN/{mwaka 2 tarakimu}/{namba ya tangazo}/{mfuatano wa tarakimu 4}
     * Mfano: JobApplication::makeInterviewNumber(12464, 15) => "NITN/26/12464/0015"
     */
    public static function makeInterviewNumber(int|string $postNumber, int $sequence, ?int $year = null): string
    {
        return sprintf(
            '%s/%s/%s/%04d',
            self::INTERVIEW_PREFIX,
            substr((string) ($year ?? date('Y')), -2),
            $postNumber,
            $sequence
        );
    }

    /**
     * Ratiba ya usaili: kila hatua yenye tarehe (au aina ya usaili bila tarehe bado).
     * Kila hatua ina: stage, date (d F Y), date_long (Wednesday, July 23, 2025), time, venue, state.
     *
     * @return array<int,array{stage:string,date:string,date_long:string,time:string,venue:string,state:string}>
     */
    public function getScheduleAttribute(): array
    {
        $today = now('Africa/Dar_es_Salaam')->startOfDay();

        $row = function (string $stage, $date, $time, $venue) use ($today) {
            return [
                'stage'     => $stage,
                'date'      => $date ? $date->format('d F Y') : 'To be communicated',
                'date_long' => $date ? $date->format('l, F j, Y') : 'To be communicated',
                'time'      => $time ? \Carbon\Carbon::parse($time)->format('g:i A') : '-',
                'venue'     => filled($venue) ? $venue : '-',
                'state'     => ! $date ? 'Pending' : ($date->copy()->startOfDay()->lte($today) ? 'Completed' : 'Upcoming'),
            ];
        };

        $rows = [];
        $type = $this->interview_type;

        // Usaili wa kwanza (Written / Practical). Ikiwa aina ni Oral na tarehe ya oral ipo, hatua hiyo ni ile ya Oral.
        if (($this->interview_at || filled($type)) && ! ($type === 'Oral' && $this->oral_at)) {
            $rows[] = $row(($type ?: 'Interview') . ' Interview', $this->interview_at, $this->interview_time, $this->interview_venue);
        }

        if ($this->oral_at) {
            $rows[] = $row('Oral Interview', $this->oral_at, $this->oral_time ?: $this->interview_time, $this->oral_venue ?: $this->interview_venue);
        }

        return $rows;
    }

    /**
     * Je, ombi hili lionyeshe Timetable (kitufe na ratiba kwenye barua)?
     * Ndiyo tu kwa Shortlisted, Interview, Oral Interview na Selected.
     * "Submitted" (Application Received) na "Not Selected" hawaoni ratiba.
     */
    public function getHasTimetableAttribute(): bool
    {
        return in_array($this->status, ['Shortlisted', 'Interview', 'Oral Interview', 'Selected'], true);
    }

    /**
     * Maelezo mafupi chini ya hali kwenye "Status reason", mf. "Selected for Oral Interview".
     * Maelezo yaliyowekwa kwenye `status_reason` yanatangulia; vinginevyo, aliyeshortlistiwa
     * (au kuendelea) na aina ya usaili anapata "Selected for {aina} Interview".
     */
    public function getReasonDetailAttribute(): ?string
    {
        if (filled($this->status_reason)) {
            return $this->status_reason;
        }

        if (filled($this->interview_type) && in_array($this->status, ['Shortlisted', 'Interview', 'Oral Interview', 'Selected'], true)) {
            return 'Selected for ' . $this->interview_type . ' Interview';
        }

        return null;
    }
}
