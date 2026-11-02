<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vacancies', function (Blueprint $table) {
            // Hiari: yakiwa tupu, maadili ya kawaida ya config/application.php yanatumika
            $table->text('letter_address')->nullable()->after('requirements');     // mstari mmoja = mstari mmoja wa anuani
            $table->text('general_conditions')->nullable()->after('letter_address'); // mstari mmoja = sharti moja
        });
    }

    public function down(): void
    {
        Schema::table('vacancies', function (Blueprint $table) {
            $table->dropColumn(['letter_address', 'general_conditions']);
        });
    }
};
