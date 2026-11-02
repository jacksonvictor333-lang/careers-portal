<?php

namespace App\Http\Controllers\Applicant;

use App\Models\WorkExperience;

class WorkExperienceController extends SectionController
{
    protected function config(): array
    {
        return [
            'slug'     => 'working-experience',
            'route'    => 'applicant.working-experience',
            'view'     => 'applicant.working-experience',
            'model'    => WorkExperience::class,
            'title'    => 'Working Experience',
            'singular' => 'Work experience',
            'subtitle' => 'Your employment history, starting with your most recent job.',
            'icon'     => 'bi-briefcase',
            'order'    => ['start_date', 'desc'],

            'fields' => [
                'employer' => [
                    'label' => 'Employer / Organisation',
                    'rules' => ['required', 'string', 'max:255'],
                ],
                'job_title' => [
                    'label' => 'Job title / Position',
                    'rules' => ['required', 'string', 'max:255'],
                ],
                'location' => [
                    'label' => 'Location',
                    'rules' => ['nullable', 'string', 'max:255'],
                    'span'  => 2,
                ],
                'start_date' => [
                    'label' => 'Start date',
                    'type'  => 'date',
                    'max'   => date('Y-m-d'),
                    'rules' => ['required', 'date', 'before_or_equal:today'],
                ],
                'end_date' => [
                    'label' => 'End date',
                    'type'  => 'date',
                    'hint'  => 'Leave empty if you still work here.',
                    'rules' => ['nullable', 'date', 'after_or_equal:start_date', 'before_or_equal:today'],
                ],
                'is_current' => [
                    'label' => 'I currently work here',
                    'type'  => 'checkbox',
                    'rules' => ['boolean'],
                    'span'  => 2,
                ],
                'duties' => [
                    'label' => 'Main duties and responsibilities',
                    'type'  => 'textarea',
                    'rules' => ['nullable', 'string', 'max:3000'],
                    'span'  => 2,
                ],
            ],

            'columns' => [
                'job_title' => 'Position',
                'employer'  => 'Employer',
                'period'    => 'Period',
                'location'  => 'Location',
            ],
        ];
    }

    /** Kama bado anafanya kazi hapo, tarehe ya kuisha haihitajiki */
    protected function prepare(array $data): array
    {
        if (! empty($data['is_current'])) {
            $data['end_date'] = null;
        }

        return $data;
    }
}
