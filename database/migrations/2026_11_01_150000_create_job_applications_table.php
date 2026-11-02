<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained()->cascadeOnDelete();
            $table->string('vacancy_title');
            $table->string('institution');
            $table->string('reference_number')->nullable();
            $table->string('status', 30)->default('Submitted');   // Submitted, Shortlisted, Interview, Oral Interview, Selected, Not Selected
            $table->date('applied_at');
            $table->date('interview_at')->nullable();             // tarehe ya interview aliyofanya
            $table->date('oral_at')->nullable();                  // tarehe ya oral interview aliyofikia
            $table->timestamps();

            $table->index(['applicant_id', 'applied_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
