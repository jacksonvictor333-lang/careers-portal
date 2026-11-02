<?php

use Database\Seeders\ProgrammeCategorySeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Categories za programu (mf. "IT and Telecoms", "Accounting and Auditing").
 *  - programmes.category_id            : kila programu iko kwenye category
 *  - academic_qualifications.programme_category : category ya elimu ya mwombaji (kwa kulinganisha na tangazo)
 *  - vacancies.required_categories      : categories zinazokubaliwa na tangazo (mstari mmoja = category moja; tupu = yoyote)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programme_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('programmes', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('name')
                  ->constrained('programme_categories')->nullOnDelete();
        });

        Schema::table('academic_qualifications', function (Blueprint $table) {
            $table->string('programme_category')->nullable()->after('programme');
        });

        Schema::table('vacancies', function (Blueprint $table) {
            $table->text('required_categories')->nullable()->after('requirements');
        });

        // Weka categories na upange programu zote
        (new ProgrammeCategorySeeder())->run();

        // Jaza category ya elimu iliyokwisha kuhifadhiwa (kwa jina la programu)
        DB::table('academic_qualifications')->whereNotNull('programme')->orderBy('id')->each(function ($q) {
            $cat = DB::table('programmes')
                ->join('programme_categories', 'programme_categories.id', '=', 'programmes.category_id')
                ->where('programmes.level', $q->level)
                ->where('programmes.name', $q->programme)
                ->value('programme_categories.name');

            if ($cat) {
                DB::table('academic_qualifications')->where('id', $q->id)->update(['programme_category' => $cat]);
            }
        });

        // Matangazo ya mfano: category inayokubaliwa
        $byRef = [
            'PSRS/2026/ICT-002' => 'IT and Telecoms',
            'PSRS/2026/REC-001' => 'Records and Information Management',
            'PSRS/2026/CLK-003' => 'Records and Information Management',
            'PSRS/2026/LAB-005' => 'Health and Medical Sciences',
            'PSRS/2026/ACC-006' => 'Accounting and Auditing',
            'PSRS/2026/LEC-007' => 'IT and Telecoms',
            'PSRS/2026/RES-008' => 'Agriculture and Natural Resources',
        ];
        foreach ($byRef as $ref => $category) {
            DB::table('vacancies')->where('reference_number', $ref)->update(['required_categories' => $category]);
        }
    }

    public function down(): void
    {
        Schema::table('vacancies', fn (Blueprint $t) => $t->dropColumn('required_categories'));
        Schema::table('academic_qualifications', fn (Blueprint $t) => $t->dropColumn('programme_category'));
        Schema::table('programmes', fn (Blueprint $t) => $t->dropConstrainedForeignId('category_id'));
        Schema::dropIfExists('programme_categories');
    }
};
