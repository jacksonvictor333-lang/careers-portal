<?php

namespace App\Http\Controllers\Applicant;

use App\Models\AcademicQualification;
use App\Models\ProgrammeCategory;
use Illuminate\Validation\Rule;

class AcademicQualificationController extends SectionController
{
    /** Vyeti/transcripts vya zamani vihifadhiwe ili staff aone Document History */
    protected function keepOldFiles(): bool
    {
        return true;
    }

    /** Je, kiwango kilichotumwa ni O-Level / A-Level? */
    private function isSecondaryRequest(): bool
    {
        return in_array(request()->input('level'), AcademicQualification::SECONDARY, true);
    }

    protected function config(): array
    {
        $year      = (int) date('Y');
        $secondary = AcademicQualification::SECONDARY;
        $colleges  = array_merge(AcademicQualification::institutionList(), [AcademicQualification::OTHER]);

        // Programu kwa kila kiwango, na category mbele ya jina (kila orodha ina "Other" mwishoni)
        $programmes = [];
        foreach (AcademicQualification::programmeOptions() as $level => $list) {
            $programmes[$level] = array_merge($list, [AcademicQualification::OTHER]);
        }
        $levelProgrammes = array_merge(
            AcademicQualification::programmesFor(request()->input('level')),
            [AcademicQualification::OTHER]
        );
        $categories = ProgrammeCategory::names();

        return [
            'slug'     => 'academic-qualifications',
            'route'    => 'applicant.academic-qualifications',
            'view'     => 'applicant.academic-qualifications',
            'model'    => AcademicQualification::class,
            'title'    => 'Academic Qualifications',
            'singular' => 'Academic qualification',
            'subtitle' => 'Add your education history, starting with your highest qualification.',
            'icon'     => 'bi-mortarboard',
            'order'    => ['end_year', 'desc'],

            'fields' => [
                'level' => [
                    'label'   => 'Level of education',
                    'type'    => 'select',
                    'options' => [
                        'O-Level (CSEE)',
                        'A-Level (ACSEE)',
                        'Certificate',
                        'Ordinary Diploma',
                        'Advanced Diploma',
                        'Bachelor Degree',
                        'Postgraduate Diploma',
                        "Master's Degree",
                        'PhD',
                    ],
                    'rules'   => ['required', 'string', 'max:150'],
                    'span'    => 2,
                ],

                // ---- Certificate kwenda juu: chagua chuo kwenye orodha ----
                'institution_select' => [
                    'label'     => 'Institution (college / university)',
                    'type'      => 'select',
                    'options'   => $colleges,
                    'hint'      => 'Not in the list? Choose "Other (not in the list)" and type the name.',
                    'star'      => true,
                    'rules'     => [
                        'nullable',
                        Rule::requiredIf(fn () => ! $this->isSecondaryRequest()),
                        Rule::in($colleges),
                    ],
                    'hide_when' => ['level', $secondary],
                ],
                // Orodha inabadilika kulingana na kiwango (Degree = programu za Degree tu, n.k.)
                'programme_select' => [
                    'label'      => 'Programme / Course',
                    'type'       => 'select',
                    'options_by' => ['field' => 'level', 'map' => $programmes],
                    'hint'       => 'Not in the list? Choose "Other (not in the list)" and type it.',
                    'star'       => true,
                    'rules'      => [
                        'nullable',
                        Rule::requiredIf(fn () => ! $this->isSecondaryRequest()),
                        Rule::in($levelProgrammes),
                    ],
                    'hide_when'  => ['level', $secondary],
                ],

                // ---- O-Level / A-Level: andika jina la shule ----
                'school_name' => [
                    'label'     => 'School name',
                    'star'      => true,
                    'rules'     => [
                        'nullable',
                        Rule::requiredIf(fn () => $this->isSecondaryRequest()),
                        'string',
                        'max:255',
                    ],
                    'show_when' => ['level', $secondary],
                ],
                'exam_number' => [
                    'label'     => 'Examination number',
                    'hint'      => 'e.g. S0123/0045/2016',
                    'star'      => true,
                    'rules'     => [
                        'nullable',
                        Rule::requiredIf(fn () => $this->isSecondaryRequest()),
                        'string',
                        'max:50',
                    ],
                    'show_when' => ['level', $secondary],
                ],

                // ---- Chuo kisichokuwa kwenye orodha ----
                'institution_other' => [
                    'label'     => 'Type the institution name',
                    'star'      => true,
                    'rules'     => [
                        'nullable',
                        Rule::requiredIf(fn () => ! $this->isSecondaryRequest()
                            && request()->input('institution_select') === AcademicQualification::OTHER),
                        'string',
                        'max:255',
                    ],
                    'show_when' => ['institution_select', [AcademicQualification::OTHER]],
                ],

                'programme_other' => [
                    'label'     => 'Type the programme / course name',
                    'star'      => true,
                    'rules'     => [
                        'nullable',
                        Rule::requiredIf(fn () => ! $this->isSecondaryRequest()
                            && request()->input('programme_select') === AcademicQualification::OTHER),
                        'string',
                        'max:255',
                    ],
                    'show_when' => ['programme_select', [AcademicQualification::OTHER]],
                ],

                // ---- Programu iliyoandikwa kwa mkono: chagua category yake (inatumika kulinganisha na matangazo) ----
                'programme_category_select' => [
                    'label'     => 'Programme category',
                    'type'      => 'select',
                    'options'   => $categories,
                    'hint'      => 'Choose the field your programme belongs to. Vacancies are matched by this category.',
                    'star'      => true,
                    'rules'     => [
                        'nullable',
                        Rule::requiredIf(fn () => ! $this->isSecondaryRequest()
                            && request()->input('programme_select') === AcademicQualification::OTHER),
                        Rule::in($categories),
                    ],
                    'show_when' => ['programme_select', [AcademicQualification::OTHER]],
                ],

                'country' => [
                    'label'   => 'Country',
                    'default' => 'Tanzania',
                    'rules'   => ['nullable', 'string', 'max:100'],
                ],
                'gpa' => [
                    'label'       => 'GPA obtained',
                    'type'        => 'number',
                    'step'        => '0.01',
                    'min'         => 0,
                    'placeholder' => 'e.g. 4.2',
                    'hint'        => 'Fill together with "GPA out of".',
                    'rules'       => ['nullable', 'numeric', 'min:0', 'max:100', 'required_with:gpa_out_of', 'lte:gpa_out_of'],
                    'hide_when'   => ['level', $secondary],
                ],
                'gpa_out_of' => [
                    'label'       => 'GPA out of (maximum)',
                    'type'        => 'number',
                    'step'        => '0.01',
                    'min'         => 0,
                    'placeholder' => 'e.g. 5',
                    'hint'        => 'The highest GPA the institution awards (e.g. 4, 4.4, 5).',
                    'rules'       => ['nullable', 'numeric', 'gt:0', 'max:100', 'required_with:gpa'],
                    'hide_when'   => ['level', $secondary],
                ],
                'start_year' => [
                    'label' => 'Year started',
                    'type'  => 'number',
                    'min'   => 1950,
                    'max'   => $year,
                    'rules' => ['nullable', 'integer', 'min:1950', 'max:' . $year],
                ],
                'end_year' => [
                    'label' => 'Year completed',
                    'type'  => 'number',
                    'min'   => 1950,
                    'max'   => $year,
                    'rules' => ['required', 'integer', 'min:1950', 'max:' . $year],
                ],
                // Cheti: kwa viwango vyote (O-Level na A-Level pia)
                'attachment_path' => [
                    'label' => 'Academic certificate',
                    'type'  => 'file',
                    'rules' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
                ],
                // Transcript: kuanzia Certificate kwenda juu tu
                'transcript_path' => [
                    'label'     => 'Transcript',
                    'type'      => 'file',
                    'rules'     => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
                    'hide_when' => ['level', $secondary],
                ],
            ],

            // Jedwali: SN, Level, Programme, Institution, Actions
            'serial'  => true,
            'columns' => [
                'level'           => 'Level',
                'programme_label' => 'Programme',
                'institution'     => 'Institution',
            ],

            // Yanaonekana akibonyeza "View" kwenye kila safu
            'details' => [
                'exam_number'     => 'Examination number',
                'end_year'        => 'Year completed',
                'country'         => 'Country',
                'start_year'      => 'Year started',
                'gpa_display'     => 'GPA',
                'programme_category' => 'Category',
                'attachment_path' => 'Certificate',
                'transcript_path' => 'Transcript',
            ],
        ];
    }

    /** Unganisha chuo/shule kuwa safu moja (institution) na futa data isiyohusu kiwango */
    protected function prepare(array $data): array
    {
        $secondary = in_array($data['level'] ?? null, AcademicQualification::SECONDARY, true);

        if ($secondary) {
            $data['institution'] = $data['school_name'] ?? null;
            $data['programme']   = null;
            $data['programme_category'] = null;
            $data['gpa']         = null;
            $data['gpa_out_of']  = null;
        } else {
            $program = $data['programme_select'] ?? null;

            $data['programme'] = ($program === AcademicQualification::OTHER)
                ? ($data['programme_other'] ?? null)
                : $program;

            // Category: ya programu iliyochaguliwa kwenye orodha, au ile aliyochagua kwa programu ya mkono
            $data['programme_category'] = ($program === AcademicQualification::OTHER)
                ? ($data['programme_category_select'] ?? null)
                : AcademicQualification::categoryFor($data['level'] ?? null, $program);

            $pick = $data['institution_select'] ?? null;

            $data['institution'] = ($pick === AcademicQualification::OTHER)
                ? ($data['institution_other'] ?? null)
                : $pick;
            $data['exam_number'] = null;
        }

        // Sehemu hizi za fomu hazina safu kwenye database
        unset(
            $data['institution_select'], $data['institution_other'], $data['school_name'],
            $data['programme_select'], $data['programme_other'], $data['programme_category_select']
        );

        return $data;
    }
}
