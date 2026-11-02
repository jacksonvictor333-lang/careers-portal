<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referee extends Model
{
    protected $guarded = ['id', 'applicant_id'];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }
}
