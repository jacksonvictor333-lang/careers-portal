<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('language_proficiencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained()->cascadeOnDelete();
            $table->string('language');
            $table->string('speaking', 30);
            $table->string('reading', 30);
            $table->string('writing', 30);
            $table->string('understanding', 30);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('language_proficiencies');
    }
};
