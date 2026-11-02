<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgrammeCategory extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function programmes(): HasMany
    {
        return $this->hasMany(Programme::class, 'category_id');
    }

    /** Majina ya categories zinazotumika, kwa mpangilio */
    public static function names(): array
    {
        return self::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->pluck('name')->all();
    }
}
