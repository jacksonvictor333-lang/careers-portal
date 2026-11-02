<?php

namespace Database\Seeders;

use App\Models\Vacancy;
use Illuminate\Database\Seeder;

/**
 * Matangazo matatu ya mfano yenye viwango tofauti vya elimu (kila moja linakubali kiwango KIMOJA tu):
 *  1) Certificate           - Assistant Registry Clerk
 *  2) Ordinary Diploma      - Records Management Assistant II
 *  3) Bachelor Degree + miaka 2 ya uzoefu - ICT Officer II
 * Mwenye kiwango cha chini au cha juu kuliko kinachohitajika anakataliwa na kuelekezwa kuangalia Academic Qualifications.
 * Inaweza kuendeshwa tena bila kurudia:  php artisan db:seed --class=VacancySeeder
 */
class VacancySeeder extends Seeder
{
    public function run(): void
    {
        $today = now('Africa/Dar_es_Salaam')->startOfDay();

        Vacancy::updateOrCreate(
            ['reference_number' => 'PSRS/2026/CLK-003'],
            [
                'title'                => 'Assistant Registry Clerk',
                'employer'             => 'Local Government Authority - Dodoma City',
                'location'             => 'Dodoma',
                'salary_scale'         => 'TGS A',
                'number_of_posts'      => 8,
                'description'          => 'Dodoma City Council invites qualified Tanzanians to fill vacant posts of Assistant Registry Clerk.',
                'duties'               => "Sort, register and distribute incoming and outgoing mail\nArrange files and keep the registry tidy\nAssist in retrieving files when requested",
                'requirements'         => "Certificate in Records and Archives Management or Office Management from a recognised institution\nBasic computer skills\nGood communication skills",
                'min_education_level'  => 'Certificate',
                'max_education_level'  => 'Certificate',
                'min_experience_years' => 0,
                'posted_at'            => $today->copy()->subDays(1),
                'closes_at'            => $today->copy()->addDays(8),
                'is_published'         => true,
            ]
        );

        Vacancy::updateOrCreate(
            ['reference_number' => 'PSRS/2026/REC-001'],
            [
                'title'                => 'Records Management Assistant II',
                'employer'             => 'Ministry of Finance',
                'location'             => 'Dodoma',
                'salary_scale'         => 'TGS B',
                'number_of_posts'      => 5,
                'description'          => 'The Ministry of Finance invites qualified Tanzanians to fill vacant posts of Records Management Assistant II.',
                'duties'               => "Receive, register and dispatch correspondence\nKeep and retrieve files and records in the registry\nMaintain the records register and file movement\nAssist in disposing of records according to the retention schedule",
                'requirements'         => "Ordinary Diploma in Records and Archives Management or Information Management from a recognised institution\nBasic computer skills\nGood communication and filing skills",
                'min_education_level'  => 'Ordinary Diploma',
                'max_education_level'  => 'Ordinary Diploma',
                'min_experience_years' => 0,
                'posted_at'            => $today->copy()->subDays(2),
                'closes_at'            => $today->copy()->addDays(10),
                'is_published'         => true,
            ]
        );

        Vacancy::updateOrCreate(
            ['reference_number' => 'PSRS/2026/ICT-002'],
            [
                'title'                => 'ICT Officer II',
                'employer'             => 'Employment Secretariat',
                'location'             => 'Dodoma',
                'salary_scale'         => 'TGS D',
                'number_of_posts'      => 2,
                'description'          => 'The Employment Secretariat invites qualified Tanzanians to fill vacant posts of ICT Officer II to support its recruitment systems.',
                'duties'               => "Develop and maintain web based recruitment systems\nAdminister databases and ensure regular backups\nProvide ICT support to staff and applicants\nPrepare ICT reports and user guides",
                'requirements'         => "Bachelor Degree in Computer Science, Information Technology, Software Engineering or related field\nAt least 2 years of working experience in systems development or support\nKnowledge of PHP/Laravel and MySQL\nGood communication and teamwork skills",
                'min_education_level'  => 'Bachelor Degree',
                'max_education_level'  => 'Bachelor Degree',
                'min_experience_years' => 2,
                'posted_at'            => $today->copy()->subDays(3),
                'closes_at'            => $today->copy()->addDays(20),
                'is_published'         => true,
            ]
        );
    }
}
