<?php

namespace Database\Seeders;

use App\Models\AwardingBody;
use App\Models\QualificationTitle;
use Illuminate\Database\Seeder;

/**
 * Inajaza (au inasasisha) taasisi na vyeti kutoka config/qualifications.php.
 * Inaweza kuendeshwa tena bila kurudia rekodi:
 *   php artisan db:seed --class=QualificationListSeeder
 */
class QualificationListSeeder extends Seeder
{
    public function run(): void
    {
        $order = 0;

        foreach (config('qualifications', []) as $body => $titles) {
            $bodyRow = AwardingBody::updateOrCreate(
                ['name' => $body],
                ['sort_order' => ++$order]
            );

            foreach (array_values($titles) as $i => $title) {
                QualificationTitle::updateOrCreate(
                    ['awarding_body_id' => $bodyRow->id, 'name' => $title],
                    ['sort_order' => $i + 1]
                );
            }
        }
    }
}
