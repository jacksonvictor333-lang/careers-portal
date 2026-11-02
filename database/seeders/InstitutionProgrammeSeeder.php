<?php

namespace Database\Seeders;

use App\Models\Institution;
use App\Models\Programme;
use Illuminate\Database\Seeder;

/**
 * Inajaza (au inasasisha) vyuo na programu kutoka config/institutions.php na config/programmes.php.
 * Inaweza kuendeshwa tena bila kurudia rekodi:
 *   php artisan db:seed --class=InstitutionProgrammeSeeder
 */
class InstitutionProgrammeSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('institutions.list', []) as $name) {
            Institution::firstOrCreate(['name' => $name]);
        }

        foreach (config('programmes', []) as $level => $names) {
            foreach (array_unique(array_values($names)) as $name) {
                Programme::firstOrCreate(['level' => $level, 'name' => $name]);
            }
        }
    }
}
