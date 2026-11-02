<?php

use Database\Seeders\EmployerSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Waajiri: kila mwajiri ana anuani yake ya barua za maombi. Matangazo yanaunganishwa kupitia employer_id.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();                  // sawa na vacancies.employer
            $table->string('addressee')->nullable();           // anayeandikiwa, mf. "The Permanent Secretary,"
            $table->text('address_lines')->nullable();         // mstari mmoja = mstari mmoja wa anuani
            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('vacancies', function (Blueprint $table) {
            $table->foreignId('employer_id')->nullable()->after('employer')
                  ->constrained('employers')->nullOnDelete();
        });

        // Waajiri wa matangazo ya mfano (anuani za majaribio) na kuunganisha matangazo yaliyopo
        (new EmployerSeeder())->run();
    }

    public function down(): void
    {
        Schema::table('vacancies', function (Blueprint $table) {
            $table->dropConstrainedForeignId('employer_id');
        });

        Schema::dropIfExists('employers');
    }
};
