<?php

namespace App\Http\Controllers\Applicant;

use App\Models\Training;

class TrainingController extends SectionController
{
    protected function config(): array
    {
        return [
            'slug'     => 'training-and-workshop',
            'route'    => 'applicant.training-and-workshop',
            'view'     => 'applicant.training-and-workshop',
            'model'    => Training::class,
            'title'    => 'Training and Workshop',
            'singular' => 'Training',
            'subtitle' => 'Short courses, trainings, seminars and workshops you have attended.',
            'icon'     => 'bi-easel',
            'order'    => ['start_date', 'desc'],

            'fields' => [
                'title' => [
                    'label' => 'Training / Workshop title',
                    'rules' => ['required', 'string', 'max:255'],
                    'span'  => 2,
                ],
                'institution' => [
                    'label' => 'Organised by',
                    'rules' => ['required', 'string', 'max:255'],
                ],
                'location' => [
                    'label' => 'Location',
                    'rules' => ['nullable', 'string', 'max:255'],
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
                    'rules' => ['nullable', 'date', 'after_or_equal:start_date'],
                ],
                'attachment_path' => [
                    'label' => 'Certificate',
                    'type'  => 'file',
                    'rules' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
                    'span'  => 2,
                ],
            ],

            'columns' => [
                'title'           => 'Training',
                'institution'     => 'Organised by',
                'period'          => 'Period',
                'attachment_path' => 'File',
            ],
        ];
    }
}
