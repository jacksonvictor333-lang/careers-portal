<?php

use Database\Seeders\VacancySeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vacancies', function (Blueprint $table) {
            // Kiwango cha juu kinachokubaliwa (hiari). min = max => nafasi ni ya kiwango kimoja tu.
            $table->string('max_education_level')->nullable()->after('min_education_level');
        });

        // Weka upya matangazo matatu ya mfano yenye viwango tofauti
        (new VacancySeeder())->run();
    }

    public function down(): void
    {
        Schema::table('vacancies', function (Blueprint $table) {
            $table->dropColumn('max_education_level');
        });
    }
};
