<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('academic_qualifications', function (Blueprint $table) {
            $table->dropColumn('grade');   // "Classification / Division" haitumiki tena
        });
    }

    public function down(): void
    {
        Schema::table('academic_qualifications', function (Blueprint $table) {
            $table->string('grade', 50)->nullable()->after('country');
        });
    }
};
