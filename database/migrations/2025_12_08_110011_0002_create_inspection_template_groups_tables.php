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
        $tablePrefix = config('pkg-inspections.table_prefix');

        // inspection template groups
        Schema::create($tablePrefix . 'inspection_template_groups', function (Blueprint $table) {
            $table->id();

            $table->string('code')
                ->nullable(false)
                ->unique()
                ->comment('Uni');
            $table->string('title')->nullable();
            $table->text('description')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        // inspection template group pivot table
        Schema::create($tablePrefix . 'inspection_template_group', function (Blueprint $table) use ($tablePrefix) {
            $table->id();
            $table->foreignId('template_id')
                ->nullable()
                ->comment('Inspection template belonging to the group')
                ->constrained($tablePrefix . 'inspection_templates', 'id');
            $table->foreignId('group_id')
                ->nullable()
                ->comment('Group inspection belongs to')
                ->constrained($tablePrefix . 'inspection_template_groups', 'id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-inspections.table_prefix');

        Schema::dropIfExists($tablePrefix . 'inspection_template_group');
        Schema::dropIfExists($tablePrefix . 'inspection_template_groups');
    }
};
