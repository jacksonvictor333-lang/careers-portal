<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Matangazo ya kazi. Mfumo mwingine unaotangaza nafasi unaingiza safu kwenye table hii
 * (is_published = 1 na closes_at ya baadaye) na tangazo linatokea kwenye ukurasa wa Vacancies.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vacancies', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();       // mf. PSRS/2026/ICT-001
            $table->string('title');                            // jina la post
            $table->string('employer');                         // mwajiri / taasisi
            $table->string('location')->nullable();
            $table->string('salary_scale')->nullable();         // mf. TGS D
            $table->unsignedSmallInteger('number_of_posts')->default(1);
            $table->text('description')->nullable();
            $table->text('duties')->nullable();                 // majukumu: mstari mmoja = kipengele kimoja
            $table->text('requirements')->nullable();           // sifa: mstari mmoja = sifa moja
            $table->string('min_education_level')->nullable();  // sawa na Level of education: "Bachelor Degree", n.k.
            $table->unsignedTinyInteger('min_experience_years')->default(0);
            $table->date('posted_at');
            $table->date('closes_at');                          // tarehe ya kufunga (siku hiyo bado inakubaliwa)
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index(['is_published', 'closes_at']);
        });

        // Matangazo ya mfano yanawekwa na migration ya 2026_11_02_150000 (baada ya safu zote kuwepo)
    }

    public function down(): void
    {
        Schema::dropIfExists('vacancies');
    }
};
