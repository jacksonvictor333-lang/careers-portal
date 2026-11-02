<?php

use Database\Seeders\ApplicationLetterSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_application_id')->unique()->constrained('job_applications')->cascadeOnDelete();
            $table->string('subject');
            $table->text('body');                      // aya zimetenganishwa kwa mstari mtupu
            $table->string('file_path')->nullable();   // PDF ya barua iliyopakiwa (hiari); ikiwepo ndiyo inayoonyeshwa
            $table->timestamps();
        });

        // Weka barua ya mfano kwa kila ombi lililopo tayari
        (new ApplicationLetterSeeder())->run();
    }

    public function down(): void
    {
        Schema::dropIfExists('application_letters');
    }
};
