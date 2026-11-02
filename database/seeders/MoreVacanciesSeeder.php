<?php

namespace Database\Seeders;

use App\Models\Vacancy;
use Illuminate\Database\Seeder;

/**
 * Matangazo matano ya ziada ya mfano, tofauti na yale matatu ya VacancySeeder, yenye viwango tofauti:
 *  1) O-Level (CSEE) tu          - Office Attendant
 *  2) Ordinary Diploma hadi Advanced Diploma (kiwango cha kuanzia hadi cha juu) - Laboratory Technician
 *  3) Bachelor Degree tu + miaka 1 - Accountant II
 *  4) Master's Degree tu + miaka 1 - Assistant Lecturer
 *  5) PhD tu + miaka 3             - Research Scientist
 * Inaweza kuendeshwa tena bila kurudia:  php artisan db:seed --class=MoreVacanciesSeeder
 */
class MoreVacanciesSeeder extends Seeder
{
    public function run(): void
    {
        $today = now('Africa/Dar_es_Salaam')->startOfDay();

        $rows = [
            [
                'reference_number'     => 'PSRS/2026/OFA-004',
                'title'                => 'Office Attendant',
                'employer'             => 'Ministry of Education, Science and Technology',
                'location'             => 'Dodoma',
                'salary_scale'         => 'TGS A',
                'number_of_posts'      => 10,
                'description'          => 'The Ministry invites qualified Tanzanians to fill vacant posts of Office Attendant.',
                'duties'               => "Keep offices clean and tidy\nDeliver messages and documents within the office\nAssist with photocopying and filing",
                'requirements'         => "Form Four certificate (CSEE) with at least a pass in Kiswahili and English\nBe physically fit and of good conduct",
                'min_education_level'  => 'O-Level (CSEE)',
                'max_education_level'  => 'O-Level (CSEE)',
                'min_experience_years' => 0,
                'posted_at'            => $today->copy()->subDays(1),
                'closes_at'            => $today->copy()->addDays(2),   // inafungwa karibuni
            ],
            [
                'reference_number'     => 'PSRS/2026/LAB-005',
                'title'                => 'Laboratory Technician II',
                'employer'             => 'Muhimbili National Hospital',
                'location'             => 'Dar es Salaam',
                'salary_scale'         => 'TGHS B',
                'number_of_posts'      => 6,
                'description'          => 'Muhimbili National Hospital invites qualified Tanzanians to fill vacant posts of Laboratory Technician II.',
                'duties'               => "Collect, label and process patient specimens\nOperate and maintain laboratory equipment\nRecord and report test results accurately\nObserve laboratory safety and infection control rules",
                'requirements'         => "Ordinary Diploma or Advanced Diploma in Medical Laboratory Sciences from a recognised institution\nRegistration with the Allied Health Professionals Council\nGood communication skills",
                'min_education_level'  => 'Ordinary Diploma',
                'max_education_level'  => 'Advanced Diploma',           // kiwango cha kuanzia hadi cha juu
                'min_experience_years' => 0,
                'posted_at'            => $today->copy()->subDays(4),
                'closes_at'            => $today->copy()->addDays(12),
            ],
            [
                'reference_number'     => 'PSRS/2026/ACC-006',
                'title'                => 'Accountant II',
                'employer'             => 'Tanzania Revenue Authority',
                'location'             => 'Dar es Salaam',
                'salary_scale'         => 'TRAS 5',
                'number_of_posts'      => 4,
                'description'          => 'The Tanzania Revenue Authority invites qualified Tanzanians to fill vacant posts of Accountant II.',
                'duties'               => "Prepare and review financial statements and reconciliations\nProcess payments and maintain ledgers\nAssist in budget preparation and monitoring\nEnsure compliance with financial regulations",
                'requirements'         => "Bachelor Degree in Accounting, Finance or related field\nAt least 1 year of relevant working experience\nCPA (T) or ACCA part qualification is an added advantage\nProficiency in accounting software",
                'min_education_level'  => 'Bachelor Degree',
                'max_education_level'  => 'Bachelor Degree',
                'min_experience_years' => 1,
                'posted_at'            => $today->copy()->subDays(5),
                'closes_at'            => $today->copy()->addDays(15),
            ],
            [
                'reference_number'     => 'PSRS/2026/LEC-007',
                'title'                => 'Assistant Lecturer - Computer Science',
                'employer'             => 'University of Dodoma',
                'location'             => 'Dodoma',
                'salary_scale'         => 'UTS 7',
                'number_of_posts'      => 3,
                'description'          => 'The University of Dodoma invites qualified Tanzanians to fill vacant posts of Assistant Lecturer in Computer Science.',
                'duties'               => "Teach undergraduate courses and supervise student projects\nPrepare teaching materials and assessments\nParticipate in research and publish findings\nProvide community services as assigned",
                'requirements'         => "Master's Degree in Computer Science, Software Engineering or related field with a good GPA\nAt least 1 year of teaching or industry experience\nEvidence of research interest",
                'min_education_level'  => "Master's Degree",
                'max_education_level'  => "Master's Degree",
                'min_experience_years' => 1,
                'posted_at'            => $today->copy()->subDays(6),
                'closes_at'            => $today->copy()->addDays(25),
            ],
            [
                'reference_number'     => 'PSRS/2026/RES-008',
                'title'                => 'Research Scientist',
                'employer'             => 'Tanzania Agricultural Research Institute',
                'location'             => 'Morogoro',
                'salary_scale'         => 'RS 3',
                'number_of_posts'      => 2,
                'description'          => 'The Institute invites qualified Tanzanians to fill vacant posts of Research Scientist in crop improvement.',
                'duties'               => "Design and lead research projects\nSupervise research assistants and students\nPublish findings in peer reviewed journals\nMobilise research funding",
                'requirements'         => "PhD in Plant Breeding, Agronomy, Biotechnology or related field\nAt least 3 years of post-qualification research experience\nA record of peer reviewed publications",
                'min_education_level'  => 'PhD',
                'max_education_level'  => 'PhD',
                'min_experience_years' => 3,
                'posted_at'            => $today->copy()->subDays(2),
                'closes_at'            => $today->copy()->addDays(30),
            ],
        ];

        foreach ($rows as $row) {
            Vacancy::updateOrCreate(
                ['reference_number' => $row['reference_number']],
                $row + ['is_published' => true]
            );
        }
    }
}
