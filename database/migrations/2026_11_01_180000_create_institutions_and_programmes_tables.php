<?php

use Database\Seeders\InstitutionProgrammeSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institutions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('programmes', function (Blueprint $table) {
            $table->id();
            $table->string('level', 100);          // mf. "Bachelor Degree" (sawa na Level of education kwenye fomu)
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['level', 'name']);
        });

        // Jaza orodha ya kuanzia kutoka config/institutions.php na config/programmes.php
        (new InstitutionProgrammeSeeder())->run();
    }

    public function down(): void
    {
        Schema::dropIfExists('programmes');
        Schema::dropIfExists('institutions');
    }
};
