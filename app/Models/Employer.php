<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employer extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function vacancies(): HasMany
    {
        return $this->hasMany(Vacancy::class);
    }

    /** Anuani kamili kama mistari: anayeandikiwa kisha mistari ya anuani (tupu ikiwa haijawekwa) */
    public function getFullAddressLinesAttribute(): array
    {
        $lines = preg_split('/\R/', (string) $this->address_lines);

        return array_values(array_filter(array_map('trim', array_merge([(string) $this->addressee], $lines))));
    }
}
