<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('position');
            $table->string('organization');
            $table->string('phone', 50);
            $table->string('email')->nullable();
            $table->string('relationship')->nullable();
            $table->unsignedTinyInteger('years_known')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referees');
    }
};
