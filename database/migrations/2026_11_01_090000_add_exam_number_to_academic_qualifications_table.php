<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('academic_qualifications', function (Blueprint $table) {
            $table->string('exam_number', 50)->nullable()->after('programme');
        });
    }

    public function down(): void
    {
        Schema::table('academic_qualifications', function (Blueprint $table) {
            $table->dropColumn('exam_number');
        });
    }
};
