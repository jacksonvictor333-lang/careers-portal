<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->string('gender', 20)->nullable()->after('last_name');
            $table->date('date_of_birth')->nullable()->after('gender');
            $table->string('place_of_birth')->nullable()->after('date_of_birth');
            $table->string('marital_status', 30)->nullable()->after('place_of_birth');
            $table->string('nationality')->nullable()->default('Tanzanian')->after('marital_status');
            $table->text('nida_number')->nullable()->after('nationality'); // inahifadhiwa ikiwa imesimbwa (encrypted)
            $table->string('alt_phone', 50)->nullable()->after('phone');
            $table->string('region')->nullable()->after('alt_phone');
            $table->string('district')->nullable()->after('region');
            $table->text('address')->nullable()->after('district');
            $table->timestamp('declaration_accepted_at')->nullable();
            $table->string('declaration_name')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropColumn([
                'gender', 'date_of_birth', 'place_of_birth', 'marital_status', 'nationality',
                'nida_number', 'alt_phone', 'region', 'district', 'address',
                'declaration_accepted_at', 'declaration_name',
            ]);
        });
    }
};
