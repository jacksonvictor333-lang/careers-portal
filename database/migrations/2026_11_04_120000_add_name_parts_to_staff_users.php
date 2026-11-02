<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff_users', function (Blueprint $table) {
            $table->string('first_name', 80)->nullable()->after('name');
            $table->string('middle_name', 80)->nullable()->after('first_name');
            $table->string('last_name', 80)->nullable()->after('middle_name');
            $table->string('phone', 20)->nullable()->after('email');
        });

        // Gawanya jina lililopo: la kwanza = first, la mwisho = last, kati = middle
        DB::table('staff_users')->orderBy('id')->each(function ($u) {
            $parts = preg_split('/\s+/', trim((string) $u->name), -1, PREG_SPLIT_NO_EMPTY);
            if (! $parts) { return; }
            $first = array_shift($parts);
            $last  = $parts ? array_pop($parts) : null;
            DB::table('staff_users')->where('id', $u->id)->update([
                'first_name' => $first, 'middle_name' => $parts ? implode(' ', $parts) : null, 'last_name' => $last,
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('staff_users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'middle_name', 'last_name', 'phone']);
        });
    }
};
