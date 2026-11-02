<?php

use App\Http\Controllers\Staff\AccountController;
use App\Http\Controllers\Staff\ApplicantActionController;
use App\Http\Controllers\Staff\ApplicantController;
use App\Http\Controllers\Staff\ArchiveController;
use App\Http\Controllers\Staff\AttachmentController;
use App\Http\Controllers\Staff\PageController;
use App\Http\Controllers\Staff\ReportController;
use App\Http\Controllers\Staff\SalaryScaleController;
use App\Http\Controllers\Staff\SchemeOfServiceController;
use App\Http\Controllers\Staff\LookupController;
use App\Http\Controllers\Staff\SettingsController;
use App\Http\Controllers\Staff\SettingsExtraController;
use App\Http\Controllers\Staff\ApplicationController;
use App\Http\Controllers\Staff\AuthController;
use App\Http\Controllers\Staff\DashboardController;
use App\Http\Controllers\Staff\EmployerController;
use App\Http\Controllers\Staff\StaffUserController;
use App\Http\Controllers\Staff\VacancyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| MFUMO WA WAFANYAKAZI (staff)
|--------------------------------------------------------------------------
| Majukumu:  viewer = kuona tu | officer = + matangazo, waajiri, hali za maombi | admin = + watumiaji
| Inapakiwa kutoka routes/web.php:  require __DIR__.'/staff.php';
*/

Route::prefix('staff')->name('staff.')->group(function () {

    // ---------- Wageni ----------
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.store');

    // ---------- Wafanyakazi wote waliongia (viewer, officer, admin) ----------
    Route::middleware(['auth:staff', 'staff.role'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/', DashboardController::class)->name('dashboard');

        Route::get('account', [AccountController::class, 'show'])->name('account');
        Route::put('account/profile', [AccountController::class, 'updateProfile'])->name('account.profile');
        Route::put('account/password', [AccountController::class, 'updatePassword'])->name('account.password');

        Route::get('vacancies', [VacancyController::class, 'index'])->name('vacancies');
        Route::get('employers', [EmployerController::class, 'index'])->name('employers');
        Route::get('applicants', [ApplicantController::class, 'index'])->name('applicants');
        Route::get('applicants/{id}', [ApplicantController::class, 'show'])->whereNumber('id')->name('applicants.show');
        Route::get('applicants/{id}/applications', [ApplicantController::class, 'applications'])->whereNumber('id')->name('applicants.applications');
        Route::get('applicants/{id}/history', [ApplicantController::class, 'history'])->whereNumber('id')->name('applicants.history');
        Route::get('applicants/{id}/check', [ApplicantController::class, 'check'])->whereNumber('id')->name('applicants.check');
        Route::get('archive', ArchiveController::class)->name('archive');
        Route::get('attachments', AttachmentController::class)->name('attachments');
        Route::get('reports', [ReportController::class, 'index'])->name('reports');
        Route::get('reports/vacancies/{id}.csv', [ReportController::class, 'csv'])->whereNumber('id')->name('reports.csv');
        Route::get('salary-scales', [SalaryScaleController::class, 'index'])->name('salary-scales');
        Route::get('scheme-of-services', [SchemeOfServiceController::class, 'index'])->name('schemes');
        Route::get('job-permits', [PageController::class, 'permits'])->name('permits');
        Route::get('tools', [PageController::class, 'tools'])->name('tools');
        Route::post('applicants/{id}/request-activation', [ApplicantActionController::class, 'requestActivation'])->whereNumber('id')->name('applicants.request');
        Route::get('files', [ApplicantController::class, 'file'])->name('files');
        Route::get('applications/{id}/letter', [ApplicantController::class, 'letter'])->whereNumber('id')->name('applications.letter');

        // ---------- Officer na admin: kuweka matangazo na kubadilisha hali za maombi ----------
        Route::middleware('staff.role:officer,admin')->group(function () {
            // (create lazima iwe kabla ya {id})
            Route::get('vacancies/create', [VacancyController::class, 'create'])->name('vacancies.create');
            Route::post('vacancies', [VacancyController::class, 'store'])->name('vacancies.store');
            Route::get('vacancies/{id}/edit', [VacancyController::class, 'edit'])->whereNumber('id')->name('vacancies.edit');
            Route::put('vacancies/{id}', [VacancyController::class, 'update'])->whereNumber('id')->name('vacancies.update');
            Route::post('vacancies/{id}/toggle', [VacancyController::class, 'toggle'])->whereNumber('id')->name('vacancies.toggle');
            Route::post('vacancies/{id}/bulk', [ApplicationController::class, 'bulk'])->whereNumber('id')->name('vacancies.bulk');

            Route::get('employers/create', [EmployerController::class, 'create'])->name('employers.create');
            Route::post('employers', [EmployerController::class, 'store'])->name('employers.store');
            Route::get('employers/{id}/edit', [EmployerController::class, 'edit'])->whereNumber('id')->name('employers.edit');
            Route::put('employers/{id}', [EmployerController::class, 'update'])->whereNumber('id')->name('employers.update');

            Route::get('salary-scales/{id}/edit', [SalaryScaleController::class, 'edit'])->whereNumber('id')->name('salary-scales.edit');
            Route::post('salary-scales', [SalaryScaleController::class, 'store'])->name('salary-scales.store');
            Route::put('salary-scales/{id}', [SalaryScaleController::class, 'update'])->whereNumber('id')->name('salary-scales.update');

            Route::get('scheme-of-services/create', [SchemeOfServiceController::class, 'create'])->name('schemes.create');
            Route::post('scheme-of-services', [SchemeOfServiceController::class, 'store'])->name('schemes.store');
            Route::get('scheme-of-services/{id}/edit', [SchemeOfServiceController::class, 'edit'])->whereNumber('id')->name('schemes.edit');
            Route::put('scheme-of-services/{id}', [SchemeOfServiceController::class, 'update'])->whereNumber('id')->name('schemes.update');

            Route::post('applicants/{id}/reset-link', [ApplicantActionController::class, 'sendResetLink'])->whereNumber('id')->name('applicants.reset-link');
            Route::post('applicants/{id}/confirm-disability', [ApplicantActionController::class, 'confirmDisability'])->whereNumber('id')->name('applicants.disability');
            Route::post('applicants/{id}/toggle-active', [ApplicantActionController::class, 'toggleActive'])->whereNumber('id')->name('applicants.active');
            Route::post('applicants/{id}/toggle-lock', [ApplicantActionController::class, 'toggleLock'])->whereNumber('id')->name('applicants.lock');

            Route::get('applications/{id}/edit', [ApplicationController::class, 'edit'])->whereNumber('id')->name('applications.edit');
            Route::put('applications/{id}', [ApplicationController::class, 'update'])->whereNumber('id')->name('applications.update');
        });

        // Ukurasa wa tangazo (baada ya create/edit ili usigongane)
        Route::get('vacancies/{id}', [VacancyController::class, 'show'])->whereNumber('id')->name('vacancies.show');

        // ---------- Admin tu: watumiaji wa staff ----------
        Route::middleware('staff.role:admin')->group(function () {
            Route::get('users', [StaffUserController::class, 'index'])->name('users');
            Route::get('users/create', [StaffUserController::class, 'create'])->name('users.create');
            Route::post('users', [StaffUserController::class, 'store'])->name('users.store');
            Route::get('users/{id}/edit', [StaffUserController::class, 'edit'])->whereNumber('id')->name('users.edit');
            Route::put('users/{id}', [StaffUserController::class, 'update'])->whereNumber('id')->name('users.update');

            Route::post('applicants/{id}/generate-password', [ApplicantActionController::class, 'generatePassword'])->whereNumber('id')->name('applicants.password');
            Route::put('applicants/{id}/email', [ApplicantActionController::class, 'updateEmail'])->whereNumber('id')->name('applicants.email.update');
            Route::delete('applicants/{id}/academic/{qid}', [ApplicantActionController::class, 'deleteAcademic'])->whereNumber(['id', 'qid'])->name('applicants.academic.delete');
            Route::delete('applicants/{id}/files', [ApplicantActionController::class, 'deleteFile'])->whereNumber('id')->name('applicants.files.delete');
            Route::post('applicant-requests/{id}/decide', [ApplicantActionController::class, 'decide'])->whereNumber('id')->name('applicants.decide');

            Route::get('settings/academic-levels', [SettingsExtraController::class, 'levels'])->name('settings.levels');
            Route::get('settings/institution-programmes', [SettingsExtraController::class, 'institutionProgrammes'])->name('settings.institution-programmes');
            Route::post('settings/institution-programmes', [SettingsExtraController::class, 'saveInstitutionProgrammes'])->name('settings.institution-programmes.save');
            Route::get('settings/documents/{key}', [SettingsExtraController::class, 'document'])->name('settings.document');
            Route::put('settings/documents/{key}', [SettingsExtraController::class, 'saveDocument'])->name('settings.document.save');

            Route::get('settings/list/{key}', [LookupController::class, 'index'])->name('settings.lookup');
            Route::post('settings/list/{key}', [LookupController::class, 'store'])->name('settings.lookup.store');
            Route::post('settings/list/{key}/bulk', [LookupController::class, 'bulk'])->name('settings.lookup.bulk');
            Route::get('settings/list/{key}/{id}/edit', [LookupController::class, 'index'])->whereNumber('id')->name('settings.lookup.edit');
            Route::put('settings/list/{key}/{id}', [LookupController::class, 'update'])->whereNumber('id')->name('settings.lookup.update');
            Route::post('settings/list/{key}/{id}/toggle', [LookupController::class, 'toggle'])->whereNumber('id')->name('settings.lookup.toggle');

            Route::get('settings/categories', [SettingsController::class, 'categories'])->name('settings.categories');
            Route::post('settings/categories', [SettingsController::class, 'storeCategory'])->name('settings.categories.store');
            Route::post('settings/categories/{id}/toggle', [SettingsController::class, 'toggleCategory'])->whereNumber('id')->name('settings.categories.toggle');
        });
    });
});
