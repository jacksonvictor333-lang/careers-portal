<?php

use Database\Seeders\StaffSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Wafanyakazi wa ofisi: wanaweka matangazo ya kazi na kuona akaunti za waombaji. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role', 20)->default('officer');   // admin | officer
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        // Mfanyakazi wa kwanza (admin) kwa majaribio: BADILISHA nenosiri baada ya kuingia
        (new StaffSeeder())->run();
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_users');
    }
};
