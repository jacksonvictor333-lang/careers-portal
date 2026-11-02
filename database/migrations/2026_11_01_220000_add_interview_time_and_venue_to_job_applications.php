<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            // Usaili wa kwanza (Written / Practical): saa na mahali
            $table->time('interview_time')->nullable()->after('interview_at');
            $table->string('interview_venue', 500)->nullable()->after('interview_time');

            // Usaili wa mahojiano (Oral): saa na mahali
            $table->time('oral_time')->nullable()->after('oral_at');
            $table->string('oral_venue', 500)->nullable()->after('oral_time');
        });
    }

    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn(['interview_time', 'interview_venue', 'oral_time', 'oral_venue']);
        });
    }
};
