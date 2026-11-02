<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('academic_qualifications', function (Blueprint $table) {
            $table->decimal('gpa', 4, 2)->nullable()->after('grade');          // alichopata, mf. 4.20
            $table->decimal('gpa_out_of', 4, 2)->nullable()->after('gpa');     // kipimo, mf. 5.00
        });
    }

    public function down(): void
    {
        Schema::table('academic_qualifications', function (Blueprint $table) {
            $table->dropColumn(['gpa', 'gpa_out_of']);
        });
    }
};
