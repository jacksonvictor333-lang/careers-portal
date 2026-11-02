<?php

namespace App\Http\Controllers\Applicant;

use App\Models\LanguageProficiency;
use Illuminate\Validation\Rule;

class LanguageProficiencyController extends SectionController
{
    protected function config(): array
    {
        $levels = ['Basic', 'Good', 'Very Good', 'Excellent'];
        $rule   = ['required', 'in:' . implode(',', $levels)];

        return [
            'slug'     => 'language-proficiency',
            'route'    => 'applicant.language-proficiency',
            'view'     => 'applicant.language-proficiency',
            'model'    => LanguageProficiency::class,
            'title'    => 'Language Proficiency',
            'singular' => 'Language',
            'subtitle' => 'Languages you can speak, read and write.',
            'icon'     => 'bi-translate',
            'order'    => ['id', 'asc'],

            'fields' => [
                'language_select' => [
                    'label'   => 'Language',
                    'type'    => 'select',
                    'options' => LanguageProficiency::CHOICES,
                    'rules'   => ['required', Rule::in(LanguageProficiency::CHOICES)],
                ],
                'language_other' => [
                    'label'     => 'Type the language',
                    'star'      => true,
                    'rules'     => [
                        'nullable',
                        Rule::requiredIf(fn () => request()->input('language_select') === LanguageProficiency::OTHER),
                        'string',
                        'max:100',
                    ],
                    'show_when' => ['language_select', [LanguageProficiency::OTHER]],
                ],
                'speaking'      => ['label' => 'Speaking',      'type' => 'select', 'options' => $levels, 'rules' => $rule],
                'reading'       => ['label' => 'Reading',       'type' => 'select', 'options' => $levels, 'rules' => $rule],
                'writing'       => ['label' => 'Writing',       'type' => 'select', 'options' => $levels, 'rules' => $rule],
                'understanding' => ['label' => 'Understanding', 'type' => 'select', 'options' => $levels, 'rules' => $rule],
            ],

            'columns' => [
                'language'      => 'Language',
                'speaking'      => 'Speaking',
                'reading'       => 'Reading',
                'writing'       => 'Writing',
                'understanding' => 'Understanding',
            ],
        ];
    }

    /** Chaguo la dropdown + lugha iliyoandikwa (Others) huunganishwa kuwa safu moja `language` */
    protected function prepare(array $data): array
    {
        $pick = $data['language_select'] ?? null;

        $data['language'] = ($pick === LanguageProficiency::OTHER)
            ? trim((string) ($data['language_other'] ?? ''))
            : $pick;

        unset($data['language_select'], $data['language_other']);

        return $data;
    }
}
