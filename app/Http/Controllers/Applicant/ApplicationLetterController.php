<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ApplicationLetter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Barua ya maombi ya kazi (PDF) pamoja na ratiba ya usaili ikiwa ipo.
 * Inaonyeshwa ndani ya dirisha (iframe) kwenye ukurasa wa My Applications.
 *
 * PDF halisi inahitaji:  composer require barryvdh/laravel-dompdf
 * Kabla ya kusakinisha, barua inaonyeshwa kama ukurasa wa HTML ili kila kitu kiendelee kufanya kazi.
 */
class ApplicationLetterController extends Controller
{
    public function __invoke(Request $request, int $id)
    {
        $applicant = Auth::guard('applicant')->user();

        // Ombi la mwombaji huyu tu: la mtu mwingine linatoa 404
        $app = $applicant->jobApplications()->findOrFail($id);

        // Barua ya mwombaji kutoka table `application_letters` (ikikosekana, ya mfano inatengenezwa)
        $letter = $app->letter ?: ApplicationLetter::createDefaultFor($app);

        // PDF ya barua iliyopakiwa ikiwepo, ndiyo inayoonyeshwa kama ilivyo
        if ($letter->file_path && Storage::disk('public')->exists($letter->file_path)) {
            $path = Storage::disk('public')->path($letter->file_path);

            return $request->boolean('download')
                ? response()->download($path, 'Application-Letter-' . Str::slug($app->vacancy_title) . '.pdf')
                : response()->file($path, ['Content-Type' => 'application/pdf']);
        }

        $data = ['applicant' => $applicant, 'app' => $app, 'letter' => $letter];

        $pdfFacade = '\\Barryvdh\\DomPDF\\Facade\\Pdf';

        if (! class_exists($pdfFacade)) {
            return response()->view('applicant.application-letter', $data + ['asPdf' => false]);
        }

        $name = 'Application-Letter-' . Str::slug($app->vacancy_title) . '.pdf';
        $pdf  = $pdfFacade::loadView('applicant.application-letter', $data + ['asPdf' => true])->setPaper('a4');

        return $request->boolean('download') ? $pdf->download($name) : $pdf->stream($name);
    }
}
