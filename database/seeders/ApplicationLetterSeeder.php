<?php

namespace Database\Seeders;

use App\Models\ApplicationLetter;
use App\Models\JobApplication;
use Illuminate\Database\Seeder;

/**
 * Inaweka barua ya mfano kwa kila ombi ambalo bado halina barua.
 * Inaweza kuendeshwa tena bila kurudia:
 *   php artisan db:seed --class=ApplicationLetterSeeder
 */
class ApplicationLetterSeeder extends Seeder
{
    public function run(): void
    {
        JobApplication::query()
            ->whereDoesntHave('letter')
            ->each(fn (JobApplication $app) => ApplicationLetter::createDefaultFor($app));
    }
}
