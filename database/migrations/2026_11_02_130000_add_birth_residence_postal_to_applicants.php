<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $cols = [
            'birth_region'       => fn (Blueprint $t) => $t->string('birth_region', 100)->nullable(),
            'birth_district'     => fn (Blueprint $t) => $t->string('birth_district', 150)->nullable(),
            'disability'         => fn (Blueprint $t) => $t->string('disability', 5)->nullable(),            // Yes / No
            'disability_details' => fn (Blueprint $t) => $t->string('disability_details', 200)->nullable(),
            'residence_country'  => fn (Blueprint $t) => $t->string('residence_country', 100)->nullable(),
            'postal_address'     => fn (Blueprint $t) => $t->string('postal_address', 150)->nullable(),     // mf. "P.O BOX 34"
            'postal_region'      => fn (Blueprint $t) => $t->string('postal_region', 100)->nullable(),
        ];

        foreach ($cols as $name => $define) {
            if (! Schema::hasColumn('applicants', $name)) {
                Schema::table('applicants', fn (Blueprint $table) => $define($table));
            }
        }
    }

    public function down(): void
    {
        foreach (['birth_region', 'birth_district', 'disability', 'disability_details', 'residence_country', 'postal_address', 'postal_region'] as $c) {
            if (Schema::hasColumn('applicants', $c)) {
                Schema::table('applicants', fn (Blueprint $table) => $table->dropColumn($c));
            }
        }
    }
};
