<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function districts(): HasMany
    {
        return $this->hasMany(District::class)->orderBy('sort_order')->orderBy('name');
    }

    private static ?array $map = null;

    /** ['Mkoa' => ['Wilaya 1', 'Wilaya 2', ...], ...] (zinazotumika tu), inasomwa mara moja kwa ombi */
    public static function map(): array
    {
        if (self::$map !== null) {
            return self::$map;
        }

        $out = [];
        $regions = self::query()
            ->where('is_active', true)
            ->with(['districts' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        foreach ($regions as $r) {
            $out[$r->name] = $r->districts->pluck('name')->all();
        }

        return self::$map = $out;
    }
}
