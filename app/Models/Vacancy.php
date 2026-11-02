<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vacancy extends Model
{
    /** Viwango vya elimu kwa mpangilio wa juu (kwa kulinganisha sifa) */
    public const EDUCATION_RANK = [
        'O-Level (CSEE)'       => 1,
        'A-Level (ACSEE)'      => 2,
        'Certificate'          => 3,
        'Ordinary Diploma'     => 4,
        'Advanced Diploma'     => 5,
        'Bachelor Degree'      => 6,
        'Postgraduate Diploma' => 7,
        "Master's Degree"      => 8,
        'PhD'                  => 9,
    ];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'posted_at'    => 'date',
            'closes_at'    => 'date',
            'is_published' => 'boolean',
        ];
    }

    /** Mwajiri (table `employers`): ana anuani ya barua za maombi */
    public function employerProfile()
    {
        return $this->belongsTo(Employer::class, 'employer_id');
    }

    /** Tangazo jipya linaunganishwa na mwajiri mwenye jina lile lile, ikiwa yupo */
    protected static function booted(): void
    {
        static::saving(function (self $v) {
            // Inalinda migration za kwanza (migrate:fresh): table/safu ya mwajiri bado haipo
            if (! \Illuminate\Support\Facades\Schema::hasColumn('vacancies', 'employer_id')) {
                return;
            }

            if (! $v->employer_id && filled($v->employer)) {
                $v->employer_id = Employer::where('name', $v->employer)->value('id');
            }
        });
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /** Tarehe ya leo (Dar es Salaam) kama maandishi Y-m-d */
    public static function today(): string
    {
        return now('Africa/Dar_es_Salaam')->toDateString();
    }

    /** Tangazo liko wazi: limechapishwa na tarehe ya kufunga haijapita (siku ya kufunga bado inakubaliwa) */
    public function getIsOpenAttribute(): bool
    {
        return $this->is_published
            && $this->closes_at
            && $this->closes_at->toDateString() >= self::today();
    }

    /** Siku zilizobaki hadi kufunga (0 = leo; hasi = limefungwa) */
    public function getDaysLeftAttribute(): int
    {
        return (int) round(\Carbon\Carbon::parse(self::today())->diffInDays($this->closes_at->copy()->startOfDay(), false));
    }

    /** @return string[] */
    public function getRequirementsListAttribute(): array
    {
        return self::lines($this->requirements);
    }

    /** @return string[] */
    public function getDutiesListAttribute(): array
    {
        return self::lines($this->duties);
    }

    private static function lines(?string $text): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R/', (string) $text))));
    }

    /**
     * Mistari ya anuani ya barua, kwa mpangilio huu:
     *  1) anuani ya tangazo lenyewe (letter_address), kama imewekwa;
     *  2) anuani ya mwajiri (table `employers`);
     *  3) anuani ya kawaida kutoka config/application.php.
     */
    public function getLetterAddressLinesAttribute(): array
    {
        $own = self::lines($this->letter_address);
        if ($own) {
            return $own;
        }

        // Mwajiri kwa employer_id; au kwa jina ikiwa tangazo liliingizwa bila employer_id
        $employer = $this->employer_id
            ? $this->employerProfile
            : (filled($this->employer) ? Employer::where('name', $this->employer)->first() : null);

        $fromEmployer = $employer ? $employer->full_address_lines : [];

        return $fromEmployer ?: array_values((array) config('application.letter_address', []));
    }

    /** @return string[] Categories za elimu zinazokubaliwa (tupu = yoyote) */
    public function getRequiredCategoriesListAttribute(): array
    {
        return self::lines($this->required_categories);
    }

    /** Masharti ya jumla: ya tangazo hili, au ya kawaida kutoka config/application.php */
    public function getConditionsListAttribute(): array
    {
        $own = self::lines($this->general_conditions);

        return $own ?: array_values((array) config('application.general_conditions', []));
    }

    /** Maelezo ya kiwango kinachohitajika: "Ordinary Diploma only", "At least Bachelor Degree", n.k. */
    public function getEducationLabelAttribute(): ?string
    {
        $min = $this->min_education_level;
        $max = $this->max_education_level;

        if ($min && $max) {
            return $min === $max ? $min . ' only' : $min . ' to ' . $max;
        }

        return $min ? 'At least ' . $min : ($max ? 'Up to ' . $max : null);
    }

    /**
     * Linganisha sifa za tangazo na wasifu wa mwombaji.
     * Elimu: kiwango cha juu cha mwombaji kinatakiwa kiwe ndani ya mipaka ya tangazo
     * (cha chini kuliko kinachohitajika au cha juu kuliko kinachokubaliwa kinakataliwa).
     *
     * @return array{ok:bool,checks:array<int,array{label:string,ok:bool,detail:string,fix:?string,fix_route:?string,fix_label:?string}>}
     */
    public function eligibility(Applicant $applicant): array
    {
        $checks = [];

        $min = $this->min_education_level ? (self::EDUCATION_RANK[$this->min_education_level] ?? 0) : 0;
        $max = $this->max_education_level ? (self::EDUCATION_RANK[$this->max_education_level] ?? null) : null;

        if ($this->min_education_level || $this->max_education_level) {
            $best     = $applicant->academicQualifications()->pluck('level')
                ->map(fn ($l) => self::EDUCATION_RANK[$l] ?? 0)->max() ?? 0;
            $bestName = array_search($best, self::EDUCATION_RANK, true) ?: null;

            $ok  = $best >= $min && ($max === null || $best <= $max);
            $fix = null;

            if (! $ok) {
                if (! $bestName) {
                    $fix = 'You have not added any academic qualification. Please add yours under Academic Qualifications.';
                } elseif ($best < $min) {
                    $fix = 'Your highest qualification (' . $bestName . ') is below the level required for this post ('
                         . $this->education_label . '). Please check your Academic Qualifications.';
                } else {
                    $fix = 'Your highest qualification (' . $bestName . ') is above the level required for this post ('
                         . $this->education_label . '). This post is only for that level. Please check your Academic Qualifications.';
                }
            }

            $checks[] = [
                'label'     => 'Education: ' . $this->education_label,
                'ok'        => $ok,
                'detail'    => 'Your highest level: ' . ($bestName ?: 'none added'),
                'fix'       => $fix,
                'fix_route' => $ok ? null : 'applicant.academic-qualifications',
                'fix_label' => $ok ? null : 'Check your Academic Qualifications',
            ];
        }

        // Category ya elimu: lazima awe na programu ya category inayokubaliwa, ndani ya kiwango kinachohitajika
        $cats = $this->required_categories_list;
        if ($cats) {
            $quals = $applicant->academicQualifications()->get(['level', 'programme', 'programme_category']);

            $inRange = $quals->filter(function ($q) use ($min, $max) {
                $r = self::EDUCATION_RANK[$q->level] ?? 0;

                return $r >= $min && ($max === null || $r <= $max);
            });

            $match  = $inRange->first(fn ($q) => in_array($q->programme_category, $cats, true));
            $have   = $inRange->pluck('programme_category')->filter()->unique()->values()->all();
            $needed = implode(', ', $cats);

            $checks[] = [
                'label'     => 'Field of study: ' . $needed,
                'ok'        => (bool) $match,
                'detail'    => $match
                    ? 'Matched: ' . $match->programme . ' (' . $match->programme_category . ')'
                    : 'Your fields at this level: ' . ($have ? implode(', ', $have) : 'none'),
                'fix'       => $match ? null : 'This post requires a qualification in: ' . $needed . '. '
                             . 'Your Academic Qualifications do not include a programme in this field at the required level. '
                             . 'Please check your Academic Qualifications.',
                'fix_route' => $match ? null : 'applicant.academic-qualifications',
                'fix_label' => $match ? null : 'Check your Academic Qualifications',
            ];
        }

        if ($this->min_experience_years > 0) {
            $today  = now('Africa/Dar_es_Salaam');
            $months = 0;
            foreach ($applicant->workExperiences()->get() as $w) {
                if (! $w->start_date) {
                    continue;
                }
                $end     = ($w->is_current || ! $w->end_date) ? $today : $w->end_date;
                $months += (int) floor(abs($w->start_date->diffInMonths($end)));
            }
            $years = intdiv($months, 12);
            $ok    = $years >= $this->min_experience_years;

            $checks[] = [
                'label'     => 'Experience: at least ' . $this->min_experience_years . ' year(s)',
                'ok'        => $ok,
                'detail'    => 'Your experience: ' . $years . ' year(s)',
                'fix'       => $ok ? null : 'This post needs at least ' . $this->min_experience_years . ' year(s) of experience; you have '
                                . $years . '. Please check your Working Experience.',
                'fix_route' => $ok ? null : 'applicant.working-experience',
                'fix_label' => $ok ? null : 'Check your Working Experience',
            ];
        }

        return ['ok' => ! in_array(false, array_column($checks, 'ok'), true), 'checks' => $checks];
    }
}
