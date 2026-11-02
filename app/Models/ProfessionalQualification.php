<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfessionalQualification extends Model
{
    public const OTHER = 'Others';

    protected $guarded = ['id', 'applicant_id'];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }

    /** Orodha ya taasisi na vyeti vyake kutoka database (zinazotumika tu), inasomwa mara moja kwa ombi */
    private static ?array $catalog = null;

    /** ['Taasisi' => ['Cheti 1', 'Cheti 2', ...], ...] */
    public static function catalog(): array
    {
        if (self::$catalog !== null) {
            return self::$catalog;
        }

        $bodies = AwardingBody::query()
            ->where('is_active', true)
            ->with(['titles' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $out = [];
        foreach ($bodies as $body) {
            $out[$body->name] = $body->titles->pluck('name')->all();
        }

        return self::$catalog = $out;
    }

    /** Taasisi zote */
    public static function bodies(): array
    {
        return array_keys(self::catalog());
    }

    /** Vyeti vya taasisi fulani */
    public static function titlesFor(?string $body): array
    {
        $map = self::catalog();

        return $body !== null && isset($map[$body]) ? array_values($map[$body]) : [];
    }

    // ---------- Kwa fomu ya kuhariri ----------

    public function getAwardingBodySelectAttribute(): ?string
    {
        if (! $this->awarding_body) {
            return null;
        }

        return in_array($this->awarding_body, self::bodies(), true) ? $this->awarding_body : self::OTHER;
    }

    public function getAwardingBodyOtherAttribute(): ?string
    {
        return $this->awarding_body_select === self::OTHER ? $this->awarding_body : null;
    }

    public function getTitleSelectAttribute(): ?string
    {
        if (! $this->title) {
            return null;
        }

        return in_array($this->title, self::titlesFor($this->awarding_body), true) ? $this->title : self::OTHER;
    }

    public function getTitleOtherAttribute(): ?string
    {
        return $this->title_select === self::OTHER ? $this->title : null;
    }
}
