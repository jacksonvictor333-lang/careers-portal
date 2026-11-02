<?php

use App\Http\Controllers\Applicant\AcademicQualificationController;
use App\Http\Controllers\Applicant\ApplicationController;
use App\Http\Controllers\Applicant\ApplicationLetterController;
use App\Http\Controllers\Applicant\AttachmentController;
use App\Http\Controllers\Applicant\AuthController;
use App\Http\Controllers\Applicant\ComputerSkillController;
use App\Http\Controllers\Applicant\CvPreviewController;
use App\Http\Controllers\Applicant\DashboardController;
use App\Http\Controllers\Applicant\DeclarationController;
use App\Http\Controllers\Applicant\LanguageProficiencyController;
use App\Http\Controllers\Applicant\MyApplicationsController;
use App\Http\Controllers\Applicant\PersonalDetailsController;
use App\Http\Controllers\Applicant\PhotoController;
use App\Http\Controllers\Applicant\ProfileController;
use App\Http\Controllers\Applicant\ProfessionalQualificationController;
use App\Http\Controllers\Applicant\RefereeController;
use App\Http\Controllers\Applicant\RegisterController;
use App\Http\Controllers\Applicant\TrainingController;
use App\Http\Controllers\Applicant\WorkExperienceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| APPLICANT PORTAL
|--------------------------------------------------------------------------
| Inapakiwa kutoka routes/web.php:  require __DIR__.'/applicant.php';
*/

Route::prefix('applicant')->name('applicant.')->group(function () {

    // ---------- Wageni ----------
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:20,1')->name('login.store');
    Route::get('register', [RegisterController::class, 'show'])->name('register');
    Route::post('register', [RegisterController::class, 'store'])->middleware('throttle:10,1')->name('register.store');

    // ---------- Walioingia ----------
    // Kiungo cha kuweka nenosiri jipya (kimetumwa na staff; signed, dakika 60)
    Route::get('reset-password/{id}', [\App\Http\Controllers\Applicant\PasswordResetController::class, 'show'])->whereNumber('id')->middleware('signed')->name('password.reset');
    Route::post('reset-password/{id}', [\App\Http\Controllers\Applicant\PasswordResetController::class, 'update'])->whereNumber('id')->middleware(['signed', 'throttle:10,1'])->name('password.reset.store');

    Route::middleware(['auth:applicant', 'applicant.active'])->group(function () {

        Route::get('dashboard', DashboardController::class)->name('dashboard');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::post('photo', [PhotoController::class, 'update'])->name('photo.update');

        // Akaunti: profile (email, nenosiri)
        Route::get('profile', [ProfileController::class, 'show'])->name('profile');
        Route::put('profile/email', [ProfileController::class, 'updateEmail'])->name('profile.email');
        Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

        // Maombi ya kazi aliyotuma
        Route::get('my-applications', MyApplicationsController::class)->name('my-applications');
        Route::get('my-applications/{id}/letter', ApplicationLetterController::class)->whereNumber('id')->name('my-applications.letter');

        // Kuhariri barua ya maombi (kabla tangazo halijafungwa)
        Route::get('my-applications/{id}/letter/edit', [ApplicationController::class, 'edit'])->whereNumber('id')->name('my-applications.letter.edit');
        Route::put('my-applications/{id}/letter', [ApplicationController::class, 'update'])->whereNumber('id')->name('my-applications.letter.update');

        // Kuomba tangazo la kazi
        Route::get('vacancies/{id}/apply', [ApplicationController::class, 'create'])->whereNumber('id')->name('vacancies.apply');
        Route::post('vacancies/{id}/apply', [ApplicationController::class, 'store'])->whereNumber('id')->name('vacancies.apply.store');

        // Personal details (rekodi moja)
        Route::get('personal-details', [PersonalDetailsController::class, 'show'])->name('personal-details');
        Route::put('personal-details/identity', [PersonalDetailsController::class, 'updateIdentity'])->name('personal-details.identity');
        Route::put('personal-details/other', [PersonalDetailsController::class, 'updateOther'])->name('personal-details.other');
        Route::put('personal-details/contact', [PersonalDetailsController::class, 'updateContact'])->name('personal-details.contact');

        // Declaration
        Route::get('declaration', [DeclarationController::class, 'show'])->name('declaration');
        Route::post('declaration', [DeclarationController::class, 'store'])->name('declaration.store');

        // CV preview
        Route::get('cv-preview', CvPreviewController::class)->name('cv-preview');

        // Sehemu za orodha: kila moja ina index, store, edit, update, destroy
        $sections = [
            'academic-qualifications'      => AcademicQualificationController::class,
            'professional-qualifications'  => ProfessionalQualificationController::class,
            'language-proficiency'         => LanguageProficiencyController::class,
            'working-experience'           => WorkExperienceController::class,
            'training-and-workshop'        => TrainingController::class,
            'computer-literacy'            => ComputerSkillController::class,
            'referees'                     => RefereeController::class,
            'other-attachments'            => AttachmentController::class,
        ];

        foreach ($sections as $slug => $controller) {
            Route::get($slug, [$controller, 'index'])->name($slug);
            Route::post($slug, [$controller, 'store'])->name($slug . '.store');
            Route::get($slug . '/{id}/edit', [$controller, 'edit'])->whereNumber('id')->name($slug . '.edit');
            Route::put($slug . '/{id}', [$controller, 'update'])->whereNumber('id')->name($slug . '.update');
            Route::delete($slug . '/{id}', [$controller, 'destroy'])->whereNumber('id')->name($slug . '.destroy');
        }
    });
});
