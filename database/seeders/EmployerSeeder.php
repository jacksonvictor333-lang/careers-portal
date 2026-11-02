<?php

namespace Database\Seeders;

use App\Models\Employer;
use App\Models\Vacancy;
use Illuminate\Database\Seeder;

/**
 * Waajiri wa matangazo ya mfano. ANUANI NI ZA MAJARIBIO ("P.O. Box 0000"): zibadilishe kuwa halisi kwenye table `employers`.
 * Pia inaunganisha kila tangazo lisilo na employer_id na mwajiri mwenye jina lile lile.
 * Inaweza kuendeshwa tena bila kurudia:  php artisan db:seed --class=EmployerSeeder
 * (Haiandiki upya anuani ulizokwisha kubadilisha: inajaza tu mwajiri asiyekuwepo.)
 */
class EmployerSeeder extends Seeder
{
    public function run(): void
    {
        $employers = [
            'Ministry of Finance'                              => ['The Permanent Secretary,', ['Ministry of Finance,', 'P.O. Box 0000,', 'Dodoma.']],
            'Employment Secretariat'                           => ['The Secretary,', ['Employment Secretariat,', 'P.O. Box 0000,', 'Dodoma.']],
            'Local Government Authority - Dodoma City'         => ['The City Director,', ['Dodoma City Council,', 'P.O. Box 0000,', 'Dodoma.']],
            'Ministry of Education, Science and Technology'    => ['The Permanent Secretary,', ['Ministry of Education, Science and Technology,', 'P.O. Box 0000,', 'Dodoma.']],
            'Muhimbili National Hospital'                      => ['The Executive Director,', ['Muhimbili National Hospital,', 'P.O. Box 0000,', 'Dar es Salaam.']],
            'Tanzania Revenue Authority'                       => ['The Commissioner General,', ['Tanzania Revenue Authority,', 'P.O. Box 0000,', 'Dar es Salaam.']],
            'University of Dodoma'                             => ['The Vice Chancellor,', ['University of Dodoma,', 'P.O. Box 0000,', 'Dodoma.']],
            'Tanzania Agricultural Research Institute'         => ['The Director General,', ['Tanzania Agricultural Research Institute,', 'P.O. Box 0000,', 'Dodoma.']],
        ];

        foreach ($employers as $name => [$addressee, $lines]) {
            Employer::firstOrCreate(
                ['name' => $name],
                ['addressee' => $addressee, 'address_lines' => implode("\n", $lines)]
            );
        }

        // Unganisha matangazo yasiyo na employer_id kwa jina la mwajiri
        Vacancy::query()->whereNull('employer_id')->get()->each(function (Vacancy $v) {
            $employer = Employer::where('name', $v->employer)->first();
            if ($employer) {
                $v->forceFill(['employer_id' => $employer->id])->saveQuietly();
            }
        });
    }
}
