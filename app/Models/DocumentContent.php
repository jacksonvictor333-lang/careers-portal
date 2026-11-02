<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

/** Maandishi yanayohaririwa na admin (Settings > Document Contents) */
class DocumentContent extends Model
{
    protected $guarded = ['id'];

    /** Maandishi kamili; $default likitumika kama jedwali/rekodi haipo */
    public static function text(string $key, string $default = ''): string
    {
        try {
            if (! Schema::hasTable('document_contents')) {
                return $default;
            }
            $body = static::where('key', $key)->value('body');
        } catch (\Throwable $e) {
            return $default;
        }

        return filled($body) ? $body : $default;
    }

    /** Mstari mmoja = kipengele kimoja */
    public static function lines(string $key, array $default = []): array
    {
        $t = static::text($key, '');
        $lines = array_values(array_filter(array_map('trim', preg_split('/\R/', $t))));

        return $lines ?: $default;
    }
}
