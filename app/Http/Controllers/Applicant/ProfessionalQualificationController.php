<?php

namespace App\Http\Controllers\Applicant;

use App\Models\ProfessionalQualification;
use Illuminate\Validation\Rule;

class ProfessionalQualificationController extends SectionController
{
    protected function config(): array
    {
        $year  = (int) date('Y');
        $other = ProfessionalQualification::OTHER;

        $bodies = array_merge(ProfessionalQualification::bodies(), [$other]);

        // Vyeti vinabadilika kulingana na taasisi (kila orodha ina "Others" mwishoni)
        $titles = [];
        foreach (ProfessionalQualification::catalog() as $body => $list) {
            $titles[$body] = array_merge(array_values($list), [$other]);
        }
        $titles[$other] = [$other];
        $bodyChosen    = request()->input('awarding_body_select');
        $validTitles   = $titles[$bodyChosen] ?? [];

        return [
            'slug'     => 'professional-qualifications',
            'route'    => 'applicant.professional-qualifications',
            'view'     => 'applicant.professional-qualifications',
            'model'    => ProfessionalQualification::class,
            'title'    => 'Professional Qualifications',
            'singular' => 'Professional qualification',
            'subtitle' => 'Professional certifications, licences and memberships (e.g. CPA, NBAA, ERB).',
            'icon'     => 'bi-award',
            'order'    => ['year_obtained', 'desc'],

            'fields' => [
                'awarding_body_select' => [
                    'label'   => 'Awarding body',
                    'type'    => 'select',
                    'options' => $bodies,
                    'hint'    => 'Not in the list? Choose "Others" and type the name.',
                    'rules'   => ['required', Rule::in($bodies)],
                ],
                'awarding_body_other' => [
                    'label'     => 'Type the awarding body',
                    'star'      => true,
                    'rules'     => [
                        'nullable',
                        Rule::requiredIf(fn () => request()->input('awarding_body_select') === $other),
                        'string',
                        'max:255',
                    ],
                    'show_when' => ['awarding_body_select', [$other]],
                ],
                'title_select' => [
                    'label'      => 'Qualification / Certification',
                    'type'       => 'select',
                    'options_by' => ['field' => 'awarding_body_select', 'map' => $titles],
                    'hint'       => 'Choose the awarding body first. Not in the list? Choose "Others".',
                    'rules'      => ['required', Rule::in($validTitles)],
                ],
                'title_other' => [
                    'label'     => 'Type the qualification / certification',
                    'star'      => true,
                    'rules'     => [
                        'nullable',
                        Rule::requiredIf(fn () => request()->input('title_select') === $other),
                        'string',
                        'max:255',
                    ],
                    'show_when' => ['title_select', [$other]],
                ],
                'year_obtained' => [
                    'label' => 'Year obtained',
                    'type'  => 'number',
                    'min'   => 1950,
                    'max'   => $year,
                    'rules' => ['required', 'integer', 'min:1950', 'max:' . $year],
                ],
                'expiry_year' => [
                    'label' => 'Expiry year (if any)',
                    'type'  => 'number',
                    'min'   => 1950,
                    'max'   => $year + 30,
                    'rules' => ['nullable', 'integer', 'min:1950', 'max:' . ($year + 30)],
                ],
                'attachment_path' => [
                    'label' => 'Certificate',
                    'type'  => 'file',
                    'rules' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
                    'span'  => 2,
                ],
            ],

            'columns' => [
                'title'           => 'Qualification',
                'awarding_body'   => 'Awarding body',
                'year_obtained'   => 'Year',
                'attachment_path' => 'File',
            ],
        ];
    }

    /** Chaguo la dropdown + jina lililoandikwa (Others) huunganishwa kuwa `awarding_body` na `title` */
    protected function prepare(array $data): array
    {
        $other = ProfessionalQualification::OTHER;

        $body = $data['awarding_body_select'] ?? null;
        $data['awarding_body'] = ($body === $other)
            ? trim((string) ($data['awarding_body_other'] ?? ''))
            : $body;

        $title = $data['title_select'] ?? null;
        $data['title'] = ($title === $other)
            ? trim((string) ($data['title_other'] ?? ''))
            : $title;

        unset(
            $data['awarding_body_select'], $data['awarding_body_other'],
            $data['title_select'], $data['title_other']
        );

        return $data;
    }
}
