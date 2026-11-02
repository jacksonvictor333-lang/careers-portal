<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

/**
 * Inajaza (au inasasisha) mikoa na wilaya kutoka config/locations.php.
 * Inaweza kuendeshwa tena bila kurudia:  php artisan db:seed --class=LocationSeeder
 */
class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $order = 0;

        foreach (config('locations.regions', []) as $name => $districts) {
            $region = Region::firstOrCreate(['name' => $name], ['sort_order' => ++$order]);

            foreach (array_values($districts) as $i => $district) {
                $region->districts()->firstOrCreate(['name' => $district], ['sort_order' => $i + 1]);
            }
        }
    }
}
