<?php

use App\Http\Controllers\VacancyController;
use Illuminate\Support\Facades\Route;

// Mwombaji aliyeingia akifungua Home anapelekwa kwenye dashboard yake; mgeni anaona ukurasa wa Home
Route::get('/', function () {
    return auth('applicant')->check()
        ? redirect()->route('applicant.dashboard')
        : view('home');
})->name('home');

// Matangazo ya kazi: yanaonekana kwa wageni na kwa waombaji
Route::get('vacancies', [VacancyController::class, 'index'])->name('vacancies');
Route::get('vacancies/{id}', [VacancyController::class, 'show'])->whereNumber('id')->name('vacancies.show');

require __DIR__ . '/applicant.php';
require __DIR__ . '/staff.php';
