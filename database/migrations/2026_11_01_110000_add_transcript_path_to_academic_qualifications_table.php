<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('academic_qualifications', function (Blueprint $table) {
            $table->string('transcript_path')->nullable()->after('attachment_path');
        });
    }

    public function down(): void
    {
        Schema::table('academic_qualifications', function (Blueprint $table) {
            $table->dropColumn('transcript_path');
        });
    }
};
