<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff_users', function (Blueprint $table) {
            $table->timestamp('last_login_at')->nullable()->after('is_active');
        });

        // Historia ya mabadiliko ya hali za maombi: nani alibadilisha, lini, na kutoka hali ipi
        Schema::create('application_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_application_id')->constrained('job_applications')->cascadeOnDelete();
            $table->foreignId('staff_user_id')->nullable()->constrained('staff_users')->nullOnDelete();
            $table->string('from_status', 30)->nullable();
            $table->string('to_status', 30);
            $table->string('note')->nullable();
            $table->timestamps();

            $table->index(['job_application_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_status_logs');

        Schema::table('staff_users', function (Blueprint $table) {
            $table->dropColumn('last_login_at');
        });
    }
};
