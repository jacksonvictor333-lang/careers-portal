<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Mwombaji anasajili kwa email na nenosiri tu; majina na simu anajaza baadaye kwenye Personal Details.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            foreach (['first_name', 'last_name', 'phone'] as $col) {
                if (Schema::hasColumn('applicants', $col)) {
                    $table->string($col)->nullable()->change();
                }
            }
        });
    }

    public function down(): void
    {
        // Haurudishwi kuwa NOT NULL: kuna akaunti ambazo majina bado hayajajazwa
    }
};
