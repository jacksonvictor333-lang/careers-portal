<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Applicant extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'date_of_birth',
        'place_of_birth',
        'marital_status',
        'nationality',
        'nida_number',
        'birth_region',
        'birth_district',
        'disability',
        'disability_details',
        'residence_country',
        'postal_address',
        'postal_region',
        'email',
        'phone',
        'alt_phone',
        'region',
        'district',
        'address',
        'password',
        'application_number',
        'photo_path',
        'is_active',
        'declaration_accepted_at',
        'declaration_name',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password'                => 'hashed',
            'is_active'               => 'boolean',
            'is_locked'               => 'boolean',
            'disability_confirmed_at' => 'datetime',
            'date_of_birth'           => 'date',
            'declaration_accepted_at' => 'datetime',
            'nida_number'             => 'encrypted',   // namba ya NIDA inahifadhiwa ikiwa imesimbwa
        ];
    }

    /**
     * Asilimia ya wasifu uliokamilika (kila sehemu ina uzito sawa) na orodha ya sehemu.
     * Orodha ya waombaji ikiwa imepakia withExists(...)/withCount('referees'), hakuna maswali ya ziada.
     *
     * @return array{0:int,1:array<int,array{label:string,done:bool,route:string}>}
     */
    public function profileCompleteness(): array
    {
        $has = fn (string $rel, string $attr) => array_key_exists($attr, $this->attributes)
            ? (bool) $this->attributes[$attr]
            : $this->{$rel}()->exists();
        $referees = array_key_exists('referees_count', $this->attributes) ? (int) $this->attributes['referees_count'] : $this->referees()->count();

        $personal = collect(['first_name', 'last_name', 'gender', 'date_of_birth', 'nationality', 'phone', 'region'])
            ->every(fn ($f) => filled($this->{$f}));

        $items = [
            ['Personal details',            $personal,                                                         'applicant.personal-details'],
            ['Profile picture',             filled($this->photo_path),                                         'applicant.dashboard'],
            ['Academic qualifications',     $has('academicQualifications', 'academic_qualifications_exists'),  'applicant.academic-qualifications'],
            ['Professional qualifications', $has('professionalQualifications', 'professional_qualifications_exists'), 'applicant.professional-qualifications'],
            ['Language proficiency',        $has('languageProficiencies', 'language_proficiencies_exists'),    'applicant.language-proficiency'],
            ['Working experience',          $has('workExperiences', 'work_experiences_exists'),                'applicant.working-experience'],
            ['Training and workshops',      $has('trainings', 'trainings_exists'),                             'applicant.training-and-workshop'],
            ['Computer literacy',           $has('computerSkills', 'computer_skills_exists'),                  'applicant.computer-literacy'],
            ['Referees (three required)',   $referees >= 3,                                                    'applicant.referees'],
            ['Declaration',                 filled($this->declaration_accepted_at),                            'applicant.declaration'],
        ];

        $list = array_map(fn ($i) => ['label' => $i[0], 'done' => (bool) $i[1], 'route' => $i[2]], $items);
        $done = count(array_filter($list, fn ($i) => $i['done']));

        return [(int) round($done / count($list) * 100), $list];
    }

    public function documentHistories()
    {
        return $this->hasMany(DocumentHistory::class);
    }

    /** Tengeneza namba ya maombi kiotomatiki: APP-2026-000001 */
    protected static function booted(): void
    {
        static::created(function (Applicant $applicant) {
            if (empty($applicant->application_number)) {
                $applicant->forceFill([
                    'application_number' => 'APP-' . now()->format('Y') . '-' . str_pad((string) $applicant->id, 6, '0', STR_PAD_LEFT),
                ])->saveQuietly();
            }
        });
    }

    // ---------- Relations ----------

    public function academicQualifications(): HasMany
    {
        return $this->hasMany(AcademicQualification::class)->orderByDesc('end_year');
    }

    public function professionalQualifications(): HasMany
    {
        return $this->hasMany(ProfessionalQualification::class)->orderByDesc('year_obtained');
    }

    public function languageProficiencies(): HasMany
    {
        return $this->hasMany(LanguageProficiency::class);
    }

    public function workExperiences(): HasMany
    {
        return $this->hasMany(WorkExperience::class)->orderByDesc('start_date');
    }

    public function trainings(): HasMany
    {
        return $this->hasMany(Training::class)->orderByDesc('start_date');
    }

    public function computerSkills(): HasMany
    {
        return $this->hasMany(ComputerSkill::class);
    }

    public function referees(): HasMany
    {
        return $this->hasMany(Referee::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    public function jobApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class)->orderByDesc('applied_at');
    }

    // ---------- Helpers ----------

    public function getFullNameAttribute(): string
    {
        $name = trim(implode(' ', array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ])));

        // Akaunti mpya haina majina bado: tumia sehemu ya kwanza ya email
        return $name !== '' ? $name : (string) strstr((string) $this->email, '@', true);
    }

    /** Herufi za kwanza: "Juma Mussa Hamisi" => "JH" */
    public function initials(): string
    {
        $parts = array_values(array_filter([$this->first_name, $this->last_name]));

        $initials = collect($parts)
            ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))
            ->implode('');

        // Bila majina: herufi ya kwanza ya email
        return $initials ?: (mb_strtoupper(mb_substr((string) $this->email, 0, 1)) ?: 'AP');
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->photo_path) {
            return null;
        }

        return asset('storage/' . $this->photo_path) . '?v=' . optional($this->updated_at)->timestamp;
    }
}
