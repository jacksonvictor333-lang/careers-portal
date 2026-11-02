<?php

namespace Database\Seeders;

use App\Models\StaffUser;
use Illuminate\Database\Seeder;

/**
 * Mfanyakazi wa kwanza kwa majaribio:
 *   Email:    admin@careers.test
 *   Password: Admin@12345   (BADILISHA baada ya kuingia mara ya kwanza)
 * Inaweza kuendeshwa tena bila kurudia:  php artisan db:seed --class=StaffSeeder
 */
class StaffSeeder extends Seeder
{
    public function run(): void
    {
        StaffUser::firstOrCreate(
            ['email' => 'admin@careers.test'],
            ['name' => 'System Administrator', 'password' => 'Admin@12345', 'role' => 'admin', 'is_active' => true]
        );
    }
}
