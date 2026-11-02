<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AwardingBody extends Model
{
    protected $table = 'awarding_bodies';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function titles(): HasMany
    {
        return $this->hasMany(QualificationTitle::class)->orderBy('sort_order')->orderBy('name');
    }
}
