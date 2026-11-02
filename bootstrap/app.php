<?php

use App\Http\Middleware\EnsureStaffRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Asiyeingia: wafanyakazi (/staff) wapelekwe login yao, waombaji login ya waombaji
        $middleware->redirectGuestsTo(fn (Request $request) => $request->is('staff', 'staff/*')
            ? route('staff.login')
            : route('applicant.login'));

        // Majukumu ya mfumo wa staff
        $middleware->alias(['staff.role' => EnsureStaffRole::class, 'applicant.active' => \App\Http\Middleware\EnsureApplicantActive::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
