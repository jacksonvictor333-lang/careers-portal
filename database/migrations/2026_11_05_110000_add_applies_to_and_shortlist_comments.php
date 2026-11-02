<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff_reasons', function (Blueprint $table) {
            $table->string('applies_to', 20)->default('All')->after('name');   // All | Normal
        });

        // Maoni ya shortlist (kwa kutoshortlist) - [maandishi, aina]
        $comments = [
            ['Lacks three year of stay to the current employer', 'All'],
            ['Application letter mismatch with CV', 'All'],
            ['Faint/Unreadable Application Letter', 'All'],
            ['Lacks a shorthand speed of 100 words per minute (100 w.p.m.)', 'All'],
            ['Lacks experience in a relevant field', 'All'],
            ['The applicant has already attended an interview for the same post', 'All'],
            ['Diploma certificate not certified by Advocate/Magistrate', 'All'],
            ['Aircraft Maintenance Engineer License not attached', 'Normal'],
            ['Already Shortlisted in the same cadre', 'Normal'],
            ['Deed Poll is not registered', 'All'],
            ['Basic not Certified by Advocate/Magistrate', 'All'],
            ['Basic Technician not Certified by Advocate/Magistrate', 'All'],
            ['Deed Poll not Attached', 'All'],
            ['Already employed in a Public Service', 'All'],
            ['Hujaambatisha barua yako ya mwisho ya kupandishwa cheo (Last Promotion Letter).', 'All'],
            ['The year of completion for JKT/JKU is before 2023', 'All'],
            ['Lacks Grade B in Behavior / Work Competence', 'All'],
            ['Application Letter not Addressed to DVC - Academic', 'All'],
            ['Application letter not routed through employer', 'All'],
            ['Trade Test Grade I/Level Three Certificate not attached', 'All'],
            ['Lacks Proof of Citizenship', 'All'],
            ['Lacks Masters Degree as per advert', 'All'],
            ['Lacks Bachelor Degree as per advert', 'All'],
            ['Application letter not attached', 'All'],
            ['Lacks Relevant Advanced Diploma as per advert', 'All'],
            ['Postgraduate Diploma Certificate not Attached', 'All'],
            ['Lacks Upper Second Class/Grade B+ as per Advert', 'All'],
            ['Diploma Certificate not Attached.', 'All'],
            ['Lacks Relevant PhD as Per Advert', 'All'],
            ['Certificate of Crash Program in Physics and Mathematics not Attached', 'All'],
            ['Certificate of Internship not Attached', 'All'],
            ['Lacks Publication Report/Research Report as per Advert', 'All'],
            ['Trade Test Level II Certificate not attached.', 'All'],
            ['Motor Vehicle Mechanics Level II Certificate not attached.', 'All'],
            ['PhD Certificate not Attached', 'All'],
            ['Lacks Relevant Professional Certificate as per Advert', 'All'],
            ['Lack Relevant Experience in Senior Position As per Advert', 'All'],
            ['Certificate not Attached', 'All'],
            ['Basic Certificate not Attached', 'All'],
            ['Not Employed in a Public Service', 'All'],
            ['Lacks Relevant Experience As per Advert', 'All'],
            ['Certificate of Registration Expired', 'All'],
            ['Training Institution not Accredited by TCU/NACTVET', 'All'],
            ['Names Mismatch', 'All'],
            ['Practicing License not Attached', 'All'],
        ];

        $now = now();
        foreach ($comments as [$name, $appliesTo]) {
            $exists = DB::table('staff_reasons')->where('type', 'shortlist_comment')->where('name', $name)->exists();
            if (! $exists) {
                DB::table('staff_reasons')->insert([
                    'type' => 'shortlist_comment', 'name' => $name, 'applies_to' => $appliesTo,
                    'is_active' => true, 'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('staff_reasons', function (Blueprint $table) {
            $table->dropColumn('applies_to');
        });
    }
};
