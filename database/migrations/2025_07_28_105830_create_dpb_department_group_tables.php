<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // department groups
        Schema::create('dpb_department_groups', function (Blueprint $table) {
            $table->comment("Department groups to manage whole groups of depatments at once");
            $table->id();
            $table->string('code')
                ->nullable(false);
                // ->unique()
                // ->comment('Unique code to identify department group in application layer');
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // pivot department group
        Schema::create('dpb_department_group', function (Blueprint $table) {
            $table->comment('Pivot to bind deparmtent and group');
            $table->id();
            $table->foreignId('department_id')
                ->nullable(false)
                ->constrained('datahub_departments', 'id');
            $table->foreignId('department_group_id')
                ->nullable(false)
                ->comment('Each department has own group as default')
                ->constrained('dpb_department_groups', 'id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dpb_department_group');
        Schema::dropIfExists('dpb_department_groups');
    }
};
