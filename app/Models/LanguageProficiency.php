<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LanguageProficiency extends Model
{
    public const OTHER   = 'Others';
    public const CHOICES = ['English', 'Swahili', self::OTHER];

    protected $guarded = ['id', 'applicant_id'];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }

    /** Kwa fomu ya kuhariri: English / Swahili / Others */
    public function getLanguageSelectAttribute(): ?string
    {
        if (! $this->language) {
            return null;
        }

        return in_array($this->language, ['English', 'Swahili'], true) ? $this->language : self::OTHER;
    }

    /** Kwa fomu ya kuhariri: lugha iliyoandikwa (isiyo English/Swahili) */
    public function getLanguageOtherAttribute(): ?string
    {
        return $this->language_select === self::OTHER ? $this->language : null;
    }
}
