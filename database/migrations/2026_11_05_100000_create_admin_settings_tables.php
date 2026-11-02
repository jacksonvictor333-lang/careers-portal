<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type', 100)->nullable();
            $table->string('region', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('nactvet_programmes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 60)->nullable();
            $table->string('name');
            $table->string('level', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('disabilities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('computer_skill_options', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Sababu / maoni: fraud_reject | account_status | shortlist_comment
        Schema::create('staff_reasons', function (Blueprint $table) {
            $table->id();
            $table->string('type', 40)->index();
            $table->string('name', 250);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Chuo kinatoa programu zipi
        Schema::create('institution_programme', function (Blueprint $table) {
            $table->foreignId('institution_id')->constrained('institutions')->cascadeOnDelete();
            $table->foreignId('programme_id')->constrained('programmes')->cascadeOnDelete();
            $table->primary(['institution_id', 'programme_id']);
        });

        Schema::create('document_contents', function (Blueprint $table) {
            $table->id();
            $table->string('key', 60)->unique();
            $table->string('title');
            $table->text('body')->nullable();
            $table->timestamps();
        });

        $now = now();
        $ins = fn ($t, $rows) => DB::table($t)->insert(array_map(fn ($r) => $r + ['created_at' => $now, 'updated_at' => $now], $rows));

        $ins('disabilities', array_map(fn ($n) => ['name' => $n], ['Physical disability', 'Visual impairment', 'Hearing impairment', 'Speech impairment', 'Albinism', 'Intellectual disability', 'Other']));
        $ins('computer_skill_options', array_map(fn ($n) => ['name' => $n], ['MS Word', 'MS Excel', 'MS PowerPoint']));
        $ins('staff_reasons', array_merge(
            array_map(fn ($n) => ['type' => 'shortlist_comment', 'name' => $n], ['Shortlisted', 'Selected for Written Interview', 'Selected for Practical Interview', 'Selected for Oral Interview']),
            array_map(fn ($n) => ['type' => 'account_status', 'name' => $n], ['Incomplete profile', 'Duplicate account', 'Requested by the applicant', 'Under investigation']),
            array_map(fn ($n) => ['type' => 'fraud_reject', 'name' => $n], ['Forged certificate', 'Invalid or fake NIN', 'Documents do not match the applicant', 'Duplicate identity']),
        ));
        $ins('document_contents', [
            ['key' => 'declaration', 'title' => 'Declaration statement', 'body' => implode("\n", [
                'All the information I have provided in this application is true, complete and correct to the best of my knowledge.',
                'I have attached genuine copies of my certificates and other supporting documents.',
                'I understand that providing false information or forged documents may lead to disqualification, or to dismissal and legal action if I am employed.',
                'I authorise the recruiting institution to verify the information and documents I have provided.',
            ])],
            ['key' => 'activation_notice', 'title' => 'Account activation notice', 'body' => 'Your account is not activated. Please wait for the Employment Secretariat to activate it. Until then you cannot fill in your profile or apply for jobs.'],
        ]);
    }

    public function down(): void
    {
        foreach (['document_contents', 'institution_programme', 'staff_reasons', 'computer_skill_options', 'disabilities', 'nactvet_programmes', 'facilities'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
