<?php

namespace Database\Seeders;

use App\Models\Applicant;
use App\Models\Institution;
use App\Models\JobApplication;
use Illuminate\Database\Seeder;

/**
 * Maombi ya mfano (pamoja na barua zake) kwa mwombaji wa kwanza, au kwa id uliyotaja:
 *   php artisan db:seed --class=SampleApplicationSeeder
 * Barua ya kila ombi inaundwa kiotomatiki kulingana na post na taasisi.
 * Inaweza kuendeshwa tena bila kurudia (ombi lenye post na taasisi ile ile halirudiwi).
 */
class SampleApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $applicant = Applicant::query()->orderBy('id')->first();

        if (! $applicant) {
            return;
        }

        // Taasisi kutoka kwenye table `institutions` (vyuo vilivyopo kwenye mfumo)
        $uni = fn (string $like, string $fallback) =>
            Institution::query()->where('name', 'like', $like)->value('name') ?: $fallback;

        $samples = [
            // post, mwajiri, hali, namba ya tangazo, siku zilizopita (kuomba), siku (interview), siku (oral), aina
            ['Assistant Lecturer - ICT',          $uni('%Dodoma%', 'University of Dodoma'),             'Oral Interview', 12464, 40, 22, 8,    'Oral'],
            ['Tutorial Assistant',                $uni('%Dar es Salaam%', 'University of Dar es Salaam'), 'Interview',      12470, 30, 5,  null, 'Written'],
            ['ICT Officer II',                    'Employment Secretariat',                             'Shortlisted',    12482, 20, null, null, 'Written'],
            ['Accounts Assistant',                'Tanzania Revenue Authority',                         'Submitted',      12490, 10, null, null, null],
            ['Artisan Grade II - Electrical',     'Tanzania Electric Supply Company Limited (TANESCO)', 'Submitted',      12495, 6,  null, null, null],
            ['Records Management Assistant',      'Ministry of Finance',                                'Not Selected',   12430, 60, 45, null, 'Written'],
        ];

        $venue = 'OFISI YA SEKRETARIETI YA AJIRA (PSRS)-DODOMA, MTAA WA MAHAKAMA, ENEO LA TAMBUKARELI.';
        $seq = 0;

        foreach ($samples as [$title, $employer, $status, $post, $applied, $int, $oral, $type]) {
            $seq++;
            $hasInterview = in_array($status, ['Shortlisted', 'Interview', 'Oral Interview', 'Not Selected'], true);

            // Kupitia uhusiano: applicant_id inawekwa yenyewe (haijazwi moja kwa moja kwa makusudi)
            $applicant->jobApplications()->firstOrCreate(
                ['vacancy_title' => $title, 'institution' => $employer],
                [
                    'status'           => $status,
                    'applied_at'       => now()->subDays($applied)->toDateString(),
                    'interview_at'     => $int ? now()->subDays($int)->toDateString() : null,
                    'oral_at'          => $oral ? now()->subDays($oral)->toDateString() : null,
                    'interview_type'   => $type,
                    'interview_time'   => $int ? '09:00:00' : null,
                    'interview_venue'  => $int ? $venue : null,
                    'oral_time'        => $oral ? '07:00:00' : null,
                    'oral_venue'       => $oral ? $venue : null,
                    'interview_number' => $hasInterview ? JobApplication::makeInterviewNumber($post, $seq) : null,
                    'reference_number' => 'REF/' . $post,
                ]
            );
        }
    }
}
