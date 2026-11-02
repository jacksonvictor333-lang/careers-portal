<?php

use Database\Seeders\QualificationListSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('awarding_bodies', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('qualification_titles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('awarding_body_id')->constrained('awarding_bodies')->cascadeOnDelete();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['awarding_body_id', 'name']);
        });

        // Jaza orodha ya kuanzia kutoka config/qualifications.php
        (new QualificationListSeeder())->run();
    }

    public function down(): void
    {
        Schema::dropIfExists('qualification_titles');
        Schema::dropIfExists('awarding_bodies');
    }
};
