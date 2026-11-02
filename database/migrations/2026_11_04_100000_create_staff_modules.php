<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            if (! Schema::hasColumn('applicants', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
            if (! Schema::hasColumn('applicants', 'is_locked')) {
                $table->boolean('is_locked')->default(false);
            }
            if (! Schema::hasColumn('applicants', 'disability_confirmed_at')) {
                $table->timestamp('disability_confirmed_at')->nullable();
                $table->unsignedBigInteger('disability_confirmed_by')->nullable();
            }
        });

        Schema::create('applicant_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20);                       // activate | unlock
            $table->string('status', 20)->default('pending'); // pending | approved | rejected
            $table->string('reason', 300)->nullable();
            $table->foreignId('requested_by')->nullable()->constrained('staff_users')->nullOnDelete();
            $table->foreignId('decided_by')->nullable()->constrained('staff_users')->nullOnDelete();
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'type']);
        });

        Schema::create('salary_scales', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique();             // mf. TGS D
            $table->string('name')->nullable();
            $table->decimal('min_salary', 14, 2)->nullable();
            $table->decimal('max_salary', 14, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('scheme_of_services', function (Blueprint $table) {
            $table->id();
            $table->string('code', 60)->unique();
            $table->string('title');                          // cheo / cadre
            $table->foreignId('salary_scale_id')->nullable()->constrained('salary_scales')->nullOnDelete();
            $table->string('min_education_level')->nullable();
            $table->string('max_education_level')->nullable();
            $table->text('required_categories')->nullable();  // mstari mmoja = category moja
            $table->unsignedTinyInteger('min_experience_years')->default(0);
            $table->text('duties')->nullable();
            $table->text('requirements')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('vacancies', function (Blueprint $table) {
            $table->foreignId('scheme_of_service_id')->nullable()->after('employer_id')->constrained('scheme_of_services')->nullOnDelete();
        });

        // Codes za TGS (bila viwango vya mshahara; admin anaweka vya kweli)
        $now = now();
        foreach (['TGS A', 'TGS B', 'TGS C', 'TGS D', 'TGS E', 'TGS F', 'TGS G', 'TGS H'] as $code) {
            if (! \Illuminate\Support\Facades\DB::table('salary_scales')->where('code', $code)->exists()) {
                \Illuminate\Support\Facades\DB::table('salary_scales')->insert(['code' => $code, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('vacancies', function (Blueprint $table) {
            $table->dropConstrainedForeignId('scheme_of_service_id');
        });
        Schema::dropIfExists('scheme_of_services');
        Schema::dropIfExists('salary_scales');
        Schema::dropIfExists('applicant_requests');
        Schema::table('applicants', function (Blueprint $table) {
            foreach (['is_locked', 'disability_confirmed_at', 'disability_confirmed_by'] as $c) {
                if (Schema::hasColumn('applicants', $c)) { $table->dropColumn($c); }
            }
        });
    }
};
