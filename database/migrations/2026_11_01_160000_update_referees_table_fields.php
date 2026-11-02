<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('referees', function (Blueprint $table) {
            $table->string('address')->nullable()->after('phone');
        });

        Schema::table('referees', function (Blueprint $table) {
            $table->dropColumn(['relationship', 'years_known']);
        });
    }

    public function down(): void
    {
        Schema::table('referees', function (Blueprint $table) {
            $table->string('relationship')->nullable();
            $table->unsignedTinyInteger('years_known')->nullable();
        });

        Schema::table('referees', function (Blueprint $table) {
            $table->dropColumn('address');
        });
    }
};
