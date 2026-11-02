<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('academic_qualification_id')->nullable()->index();
            $table->string('level')->nullable();
            $table->string('doc_type', 20);          // Certificate | Transcript
            $table->string('path');
            $table->string('action', 20);            // uploaded | replaced | removed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_histories');
    }
};
