<?php

namespace Database\Seeders;

use App\Models\Programme;
use App\Models\ProgrammeCategory;
use Illuminate\Database\Seeder;

/**
 * Inaweka categories kutoka config/programme_categories.php na kupanga kila programu isiyo na category
 * kulingana na maneno muhimu kwenye jina lake. Haibadilishi programu zenye category tayari
 * (kwa hiyo mabadiliko yako ya mkono yanabaki). Inaweza kuendeshwa tena:
 *   php artisan db:seed --class=ProgrammeCategorySeeder
 */
class ProgrammeCategorySeeder extends Seeder
{
    public function run(): void
    {
        $map = config('programme_categories', []);
        $ids = [];
        $order = 0;

        foreach (array_keys($map) as $name) {
            $ids[$name] = ProgrammeCategory::firstOrCreate(['name' => $name], ['sort_order' => ++$order])->id;
        }

        Programme::query()->whereNull('category_id')->each(function (Programme $p) use ($map, $ids) {
            $low  = mb_strtolower($p->name) . ' ';
            $hit  = 'Other / General';

            foreach ($map as $category => $keywords) {
                foreach ($keywords as $k) {
                    if (str_contains($low, $k)) {
                        $hit = $category;
                        break 2;
                    }
                }
            }

            $p->forceFill(['category_id' => $ids[$hit] ?? null])->saveQuietly();
        });
    }
}
