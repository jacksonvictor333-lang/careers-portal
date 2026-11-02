<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicQualification extends Model
{
    /** Faili zinazofuatiliwa kwenye Document History */
    public const DOC_FIELDS = ['attachment_path' => 'Certificate', 'transcript_path' => 'Transcript'];

    protected static function booted(): void
    {
        $log = function (self $m, string $type, string $path, string $action) {
            if (! \Illuminate\Support\Facades\Schema::hasTable('document_histories')) {
                return;
            }
            DocumentHistory::create([
                'applicant_id' => $m->applicant_id, 'academic_qualification_id' => $m->id,
                'level' => $m->level, 'doc_type' => $type, 'path' => $path, 'action' => $action,
            ]);
        };

        static::created(function (self $m) use ($log) {
            foreach (self::DOC_FIELDS as $f => $type) {
                if ($m->{$f}) { $log($m, $type, $m->{$f}, 'uploaded'); }
            }
        });

        static::updated(function (self $m) use ($log) {
            foreach (self::DOC_FIELDS as $f => $type) {
                if (! $m->wasChanged($f)) { continue; }
                $old = $m->getOriginal($f);
                if ($old) { $log($m, $type, $old, $m->{$f} ? 'replaced' : 'removed'); }
                if ($m->{$f}) { $log($m, $type, $m->{$f}, 'uploaded'); }
            }
        });

        static::deleting(function (self $m) use ($log) {
            foreach (self::DOC_FIELDS as $f => $type) {
                if ($m->{$f}) { $log($m, $type, $m->{$f}, 'removed'); }
            }
        });
    }

    /** Viwango vya sekondari: vinatumia jina la shule na namba ya mtihani */
    public const SECONDARY = [
        'O-Level (CSEE)',
        'A-Level (ACSEE)',
    ];

    /** Vifupisho vinavyoonekana kwenye safu ya Programme kwa O-Level na A-Level */
    public const ABBREVIATIONS = [
        'O-Level (CSEE)'  => 'CSEE',
        'A-Level (ACSEE)' => 'ACSEE',
    ];

    /** Chaguo la mwisho kwenye orodha ya vyuo: mtumiaji anaandika jina mwenyewe */
    public const OTHER = 'Other (not in the list)';

    protected $guarded = ['id', 'applicant_id'];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }

    // ---------- Orodha ya vyuo ----------

    private static ?array $institutions = null;
    private static ?array $programmeMap  = null;

    /** Vyuo vinavyotumika (kutoka table `institutions`), kwa alfabeti */
    public static function institutionList(): array
    {
        if (self::$institutions === null) {
            $list = Institution::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->pluck('name')
                ->all();

            self::$institutions = array_values($list);
        }

        return self::$institutions;
    }

    private static ?array $programmeRows = null;

    /** Programu zote zinazotumika pamoja na category yake: [['level','name','category'], ...] (inasomwa mara moja) */
    private static function programmeRows(): array
    {
        if (self::$programmeRows === null) {
            self::$programmeRows = Programme::query()
                ->where('is_active', true)
                ->with('category:id,name')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(['level', 'name', 'category_id'])
                ->map(fn ($p) => ['level' => $p->level, 'name' => $p->name, 'category' => optional($p->category)->name])
                ->all();
        }

        return self::$programmeRows;
    }

    /** ['Level' => ['Programme 1', ...], ...] kutoka table `programmes` (zinazotumika tu) */
    public static function programmeCatalog(): array
    {
        if (self::$programmeMap === null) {
            $map = [];
            foreach (self::programmeRows() as $row) {
                $map[$row['level']][] = $row['name'];
            }
            self::$programmeMap = $map;
        }

        return self::$programmeMap;
    }

    /**
     * Chaguo za dropdown ya programu, na category mbele ya jina:
     * ['Level' => [['v' => 'Jina', 'l' => 'Jina (Category)'], ...], ...]
     */
    public static function programmeOptions(): array
    {
        $out = [];
        foreach (self::programmeRows() as $row) {
            $out[$row['level']][] = [
                'v' => $row['name'],
                'l' => $row['name'] . ($row['category'] ? ' (' . $row['category'] . ')' : ''),
            ];
        }

        return $out;
    }

    /** Category ya programu (kwa level na jina), au null ikiwa haipo kwenye orodha */
    public static function categoryFor(?string $level, ?string $name): ?string
    {
        foreach (self::programmeRows() as $row) {
            if ($row['level'] === $level && $row['name'] === $name) {
                return $row['category'];
            }
        }

        return null;
    }

    /** Programu za kiwango fulani */
    public static function programmesFor(?string $level): array
    {
        $map = self::programmeCatalog();

        return $level !== null && isset($map[$level]) ? array_values($map[$level]) : [];
    }

    public function isSecondary(): bool
    {
        return in_array($this->level, self::SECONDARY, true);
    }

    /** Kwa fomu ya kuhariri: chuo kilichochaguliwa kwenye orodha (au "Other") */
    public function getInstitutionSelectAttribute(): ?string
    {
        if (! $this->institution || $this->isSecondary()) {
            return null;
        }

        return in_array($this->institution, self::institutionList(), true)
            ? $this->institution
            : self::OTHER;
    }

    /** Kwa fomu ya kuhariri: jina la chuo lililoandikwa (kisichokuwa kwenye orodha) */
    public function getInstitutionOtherAttribute(): ?string
    {
        if (! $this->institution || $this->isSecondary()) {
            return null;
        }

        return in_array($this->institution, self::institutionList(), true) ? null : $this->institution;
    }

    /** Kwa fomu ya kuhariri: programu iliyochaguliwa kwenye orodha ya kiwango hicho (au "Other") */
    public function getProgrammeSelectAttribute(): ?string
    {
        if (! $this->programme || $this->isSecondary()) {
            return null;
        }

        return in_array($this->programme, self::programmesFor($this->level), true)
            ? $this->programme
            : self::OTHER;
    }

    /** Kwa fomu ya kuhariri: category iliyochaguliwa wakati programu iliandikwa kwa mkono */
    public function getProgrammeCategorySelectAttribute(): ?string
    {
        return $this->programme_select === self::OTHER ? $this->programme_category : null;
    }

    /** Kwa fomu ya kuhariri: programu iliyoandikwa (isiyokuwa kwenye orodha) */
    public function getProgrammeOtherAttribute(): ?string
    {
        if (! $this->programme || $this->isSecondary()) {
            return null;
        }

        return in_array($this->programme, self::programmesFor($this->level), true) ? null : $this->programme;
    }

    /** Kwa fomu ya kuhariri: jina la shule (O-Level / A-Level) */
    public function getSchoolNameAttribute(): ?string
    {
        return $this->isSecondary() ? $this->institution : null;
    }

    // ---------- Maelezo ya kuonyesha ----------

    /** Safu ya Programme: CSEE / ACSEE kwa sekondari, jina la programu kwa wengine */
    public function getProgrammeLabelAttribute(): ?string
    {
        return self::ABBREVIATIONS[$this->level] ?? $this->programme;
    }

    /** Programme (Certificate kwenda juu) au namba ya mtihani (O-Level/A-Level) */
    public function getDetailAttribute(): ?string
    {
        if ($this->programme) {
            return $this->programme;
        }

        return $this->exam_number ? 'Exam No. ' . $this->exam_number : null;
    }

    /** "4.2 / 5" (alichopata / kipimo), au null kama hajajaza */
    public function getGpaDisplayAttribute(): ?string
    {
        if ($this->gpa === null || $this->gpa_out_of === null) {
            return null;
        }

        $fmt = fn ($n) => rtrim(rtrim(number_format((float) $n, 2, '.', ''), '0'), '.');

        return $fmt($this->gpa) . ' / ' . $fmt($this->gpa_out_of);
    }
}
