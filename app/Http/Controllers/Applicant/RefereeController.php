<?php

namespace App\Http\Controllers\Applicant;

use App\Models\Referee;

class RefereeController extends SectionController
{
    protected function config(): array
    {
        return [
            'slug'     => 'referees',
            'route'    => 'applicant.referees',
            'view'     => 'applicant.referees',
            'model'    => Referee::class,
            'title'    => 'Referees',
            'singular' => 'Referee',
            'subtitle' => 'People who can vouch for your character and work. Add three referees.',
            'icon'     => 'bi-people',
            'order'    => ['id', 'asc'],

            'fields' => [
                'name' => [
                    'label' => 'Referee Full Name',
                    'rules' => ['required', 'string', 'max:255'],
                ],
                'organization' => [
                    'label' => 'Institution/Organization',
                    'rules' => ['required', 'string', 'max:255'],
                ],
                'phone' => [
                    'label'       => 'Telephone Number',
                    'default'     => '+255',
                    'placeholder' => '+255 7XX XXX XXX',
                    'rules'       => ['required', 'string', 'max:50', 'regex:/^\+?[0-9 ]{9,20}$/'],
                ],
                'position' => [
                    'label'       => 'Title/Position',
                    'placeholder' => 'e.g. Head of ICT, Lecturer, Director',
                    'rules'       => ['required', 'string', 'max:255'],
                ],
                'address' => [
                    'label' => 'Address',
                    'rules' => ['required', 'string', 'max:255'],
                ],
                'email' => [
                    'label' => 'Email Address',
                    'type'  => 'email',
                    'rules' => ['required', 'email', 'max:255'],
                ],
            ],

            'columns' => [
                'name'         => 'Name',
                'position'     => 'Title/Position',
                'organization' => 'Institution',
                'phone'        => 'Phone',
                'email'        => 'Email',
            ],
        ];
    }
}
