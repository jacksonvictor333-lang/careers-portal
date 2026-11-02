<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicantRequest extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['decided_at' => 'datetime'];
    }

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }

    public function requester()
    {
        return $this->belongsTo(StaffUser::class, 'requested_by');
    }

    public function decider()
    {
        return $this->belongsTo(StaffUser::class, 'decided_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'unlock' ? 'Unlock' : 'Activation';
    }
}
