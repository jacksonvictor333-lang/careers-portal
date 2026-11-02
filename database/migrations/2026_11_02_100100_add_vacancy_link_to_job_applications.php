<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->foreignId('vacancy_id')->nullable()->after('applicant_id')
                  ->constrained('vacancies')->nullOnDelete();

            // Mwombaji mmoja anaomba tangazo moja mara moja tu
            $table->unique(['applicant_id', 'vacancy_id']);
        });

        // Viambatisho (kutoka "Other Attachments") alivyoambatisha kwenye ombi
        Schema::create('application_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_application_id')->constrained('job_applications')->cascadeOnDelete();
            $table->foreignId('attachment_id')->constrained('attachments')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['job_application_id', 'attachment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_attachments');

        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropUnique(['applicant_id', 'vacancy_id']);
            $table->dropConstrainedForeignId('vacancy_id');
        });
    }
};
