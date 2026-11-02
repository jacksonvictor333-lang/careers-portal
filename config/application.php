<?php

/*
|--------------------------------------------------------------------------
| Masharti ya jumla na anuani ya barua ya maombi (maadili ya kawaida)
|--------------------------------------------------------------------------
| Tangazo likiwa na `letter_address` au `general_conditions` kwenye table ya `vacancies`,
| hayo yanatumika kwa tangazo hilo; vinginevyo maadili haya ya kawaida yanatumika.
| BADILISHA anuani hapa iwe anuani kamili ya taasisi yenu.
*/
return [
    // Mstari mmoja = mstari mmoja wa anuani
    'letter_address' => [
        'The Secretary,',
        'Public Service Recruitment Secretariat,',
        'P.O. Box 0000,',
        'Dodoma.',
    ],

    // Sharti moja kwa kila kipengele
    'general_conditions' => [
        'All applicants must be citizens of Tanzania.',
        'Attach an up-to-date CV with reliable contacts.',
        'Attach copies of your academic and professional certificates, and your birth certificate. Testimonials and statements of results are not accepted.',
        'Attach a recent passport size photo.',
        'Applicants employed in the Public Service must route their applications through their employers.',
        'Persons with disability are encouraged to apply.',
        'Forged certificates will lead to legal action.',
        'Only shortlisted candidates will be informed of the interview date.',
        'Apply through this portal only, before the closing date. Posted or hand delivered letters are not accepted.',
    ],
];
