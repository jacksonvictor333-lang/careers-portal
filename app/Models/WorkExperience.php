<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkExperience extends Model
{
    protected $guarded = ['id', 'applicant_id'];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date'   => 'date',
            'is_current' => 'boolean',
        ];
    }

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }

    /** "Jan 2020 – Present" */
    public function getPeriodAttribute(): string
    {
        $start = $this->start_date ? $this->start_date->format('M Y') : '—';
        $end   = $this->is_current ? 'Present' : ($this->end_date ? $this->end_date->format('M Y') : '—');

        return $start . ' – ' . $end;
    }
}
