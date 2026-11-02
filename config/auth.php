<?php

return [

    /*
    | Mlinzi wa kawaida (web) bado upo; waombaji wanatumia 'applicant' na wafanyakazi wa ofisi 'staff'.
    */
    'defaults' => [
        'guard'     => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    'guards' => [
        'web' => [
            'driver'   => 'session',
            'provider' => 'users',
        ],
        'applicant' => [
            'driver'   => 'session',
            'provider' => 'applicants',
        ],
        'staff' => [
            'driver'   => 'session',
            'provider' => 'staff_users',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model'  => env('AUTH_MODEL', App\Models\User::class),
        ],
        'applicants' => [
            'driver' => 'eloquent',
            'model'  => App\Models\Applicant::class,
        ],
        'staff_users' => [
            'driver' => 'eloquent',
            'model'  => App\Models\StaffUser::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table'    => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire'   => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
