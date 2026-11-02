<?php

namespace App\Support;

use App\Models\ProgrammeCategory;
use App\Models\Vacancy;
use Illuminate\Support\Facades\DB;

/**
 * Orodha za Settings (admin) zinazoendeshwa na config moja: kila kipengele kina jedwali, sehemu za fomu na safu za orodha.
 * Kuongeza orodha mpya = kuongeza kipengele hapa (na jedwali lake).
 */
class StaffLookups
{
    public static function all(): array
    {
        $levels = fn () => array_combine(array_keys(Vacancy::EDUCATION_RANK), array_keys(Vacancy::EDUCATION_RANK));
        $reason = fn (string $type, string $title, string $icon, string $note) => [
            'title' => $title, 'icon' => $icon, 'note' => $note, 'table' => 'staff_reasons',
            'query' => fn () => DB::table('staff_reasons')->where('type', $type)->select('staff_reasons.*')->orderBy('name'),
            'fields' => [['name' => 'name', 'label' => 'Text', 'type' => 'text', 'required' => true, 'max' => 250]],
            'columns' => [['label' => 'Text', 'field' => 'name']],
            'search' => ['name'], 'fixed' => ['type' => $type], 'unique' => ['type', 'name'],
        ];
        $withType = function (array $def) {
            $def['fields'][] = ['name' => 'applies_to', 'label' => 'Type', 'type' => 'select', 'options' => fn () => ['All' => 'All', 'Normal' => 'Normal']];
            $def['columns'][] = ['label' => 'Type', 'field' => 'applies_to'];
            $def['bulk'] = true;

            return $def;
        };

        return [
            'facilities' => [
                'title' => 'Manage Facilities', 'icon' => 'bi-hospital', 'table' => 'facilities',
                'note' => 'Facilities or work stations where posts are located.',
                'query' => fn () => DB::table('facilities')->select('facilities.*')->orderBy('name'),
                'fields' => [
                    ['name' => 'name', 'label' => 'Facility name', 'type' => 'text', 'required' => true, 'max' => 255],
                    ['name' => 'type', 'label' => 'Type', 'type' => 'text', 'max' => 100],
                    ['name' => 'region', 'label' => 'Region', 'type' => 'select', 'options' => fn () => DB::table('regions')->orderBy('name')->pluck('name', 'name')->all()],
                    ['name' => 'district', 'label' => 'District', 'type' => 'text', 'max' => 100],
                ],
                'columns' => [['label' => 'Facility', 'field' => 'name'], ['label' => 'Type', 'field' => 'type'], ['label' => 'Region', 'field' => 'region'], ['label' => 'District', 'field' => 'district']],
                'search' => ['name', 'region', 'district'],
            ],
            'academic-programmes' => [
                'title' => 'Academic Programmes', 'icon' => 'bi-journal-bookmark', 'table' => 'programmes',
                'note' => 'Programmes shown to applicants under Academic Qualifications. The category is used to match applicants to adverts.',
                'query' => fn () => DB::table('programmes')->leftJoin('programme_categories as c', 'c.id', '=', 'programmes.category_id')
                    ->select('programmes.*', 'c.name as category_name')->orderBy('programmes.level')->orderBy('programmes.name'),
                'fields' => [
                    ['name' => 'level', 'label' => 'Level', 'type' => 'select', 'required' => true, 'options' => $levels],
                    ['name' => 'name', 'label' => 'Programme name', 'type' => 'text', 'required' => true, 'max' => 255],
                    ['name' => 'category_id', 'label' => 'Category', 'type' => 'select', 'options' => fn () => DB::table('programme_categories')->where('is_active', true)->orderBy('name')->pluck('name', 'id')->all()],
                ],
                'columns' => [['label' => 'Level', 'field' => 'level'], ['label' => 'Programme', 'field' => 'name'], ['label' => 'Category', 'field' => 'category_name']],
                'search' => ['programmes.name', 'programmes.level', 'c.name'], 'unique' => ['level', 'name'],
            ],
            'academic-institutions' => [
                'title' => 'Academic Institutions', 'icon' => 'bi-bank', 'table' => 'institutions',
                'note' => 'Colleges and universities shown to applicants. Another system can also add institutions to this table.',
                'query' => fn () => DB::table('institutions')->select('institutions.*')->orderBy('name'),
                'fields' => [['name' => 'name', 'label' => 'Institution name', 'type' => 'text', 'required' => true, 'max' => 255]],
                'columns' => [['label' => 'Institution', 'field' => 'name']],
                'search' => ['name'], 'unique' => ['name'],
            ],
            'nactvet-programmes' => [
                'title' => 'NACTVET Programmes', 'icon' => 'bi-tools', 'table' => 'nactvet_programmes',
                'note' => 'Technical programmes registered with NACTVET.',
                'query' => fn () => DB::table('nactvet_programmes')->select('nactvet_programmes.*')->orderBy('name'),
                'fields' => [
                    ['name' => 'code', 'label' => 'Code', 'type' => 'text', 'max' => 60],
                    ['name' => 'name', 'label' => 'Programme name', 'type' => 'text', 'required' => true, 'max' => 255],
                    ['name' => 'level', 'label' => 'Level', 'type' => 'select', 'options' => fn () => ['Certificate' => 'Certificate', 'Ordinary Diploma' => 'Ordinary Diploma', 'Advanced Diploma' => 'Advanced Diploma']],
                ],
                'columns' => [['label' => 'Code', 'field' => 'code'], ['label' => 'Programme', 'field' => 'name'], ['label' => 'Level', 'field' => 'level']],
                'search' => ['name', 'code'],
            ],
            'disabilities' => [
                'title' => 'Manage Disabilities', 'icon' => 'bi-person-wheelchair', 'table' => 'disabilities',
                'note' => 'Types of disability.',
                'query' => fn () => DB::table('disabilities')->select('disabilities.*')->orderBy('name'),
                'fields' => [['name' => 'name', 'label' => 'Disability', 'type' => 'text', 'required' => true, 'max' => 255]],
                'columns' => [['label' => 'Disability', 'field' => 'name']], 'search' => ['name'], 'unique' => ['name'],
            ],
            'computer-skills' => [
                'title' => 'Computer Skills', 'icon' => 'bi-pc-display', 'table' => 'computer_skill_options',
                'note' => 'Computer skills an applicant can be asked about.',
                'query' => fn () => DB::table('computer_skill_options')->select('computer_skill_options.*')->orderBy('name'),
                'fields' => [['name' => 'name', 'label' => 'Skill', 'type' => 'text', 'required' => true, 'max' => 255]],
                'columns' => [['label' => 'Skill', 'field' => 'name']], 'search' => ['name'], 'unique' => ['name'],
            ],
            'fraud-reject-reasons' => $reason('fraud_reject', 'Fraud Flag Reject Reasons', 'bi-flag', 'Reasons used when a fraud flag is rejected.'),
            'account-status-reasons' => $reason('account_status', 'Account Status Reasons', 'bi-person-lock', 'Reasons for activating, deactivating or locking an account.'),
            'shortlist-comments' => $withType($reason('shortlist_comment', 'Manage Shortlist Comments', 'bi-chat-left-text', 'Ready-made comments. They appear as a choice when you update an application status.')),
            'professional-institutions' => [
                'title' => 'Professional Institutions', 'icon' => 'bi-building-check', 'table' => 'awarding_bodies',
                'note' => 'Awarding bodies shown under Professional Qualifications.',
                'query' => fn () => DB::table('awarding_bodies')->select('awarding_bodies.*')->orderBy('name'),
                'fields' => [['name' => 'name', 'label' => 'Institution name', 'type' => 'text', 'required' => true, 'max' => 255]],
                'columns' => [['label' => 'Institution', 'field' => 'name']], 'search' => ['name'], 'unique' => ['name'],
            ],
            'professional-courses' => [
                'title' => 'Professional Courses', 'icon' => 'bi-award', 'table' => 'qualification_titles',
                'note' => 'Qualification titles shown after the applicant picks an awarding body.',
                'query' => fn () => DB::table('qualification_titles')->leftJoin('awarding_bodies as b', 'b.id', '=', 'qualification_titles.awarding_body_id')
                    ->select('qualification_titles.*', 'b.name as body_name')->orderBy('b.name')->orderBy('qualification_titles.name'),
                'fields' => [
                    ['name' => 'awarding_body_id', 'label' => 'Institution', 'type' => 'select', 'required' => true, 'options' => fn () => DB::table('awarding_bodies')->orderBy('name')->pluck('name', 'id')->all()],
                    ['name' => 'name', 'label' => 'Course / qualification', 'type' => 'text', 'required' => true, 'max' => 255],
                ],
                'columns' => [['label' => 'Institution', 'field' => 'body_name'], ['label' => 'Course', 'field' => 'name']],
                'search' => ['qualification_titles.name', 'b.name'], 'unique' => ['awarding_body_id', 'name'],
            ],
        ];
    }

    public static function find(string $key): array
    {
        $all = self::all();
        abort_unless(isset($all[$key]), 404);

        return $all[$key] + ['key' => $key];
    }
}
