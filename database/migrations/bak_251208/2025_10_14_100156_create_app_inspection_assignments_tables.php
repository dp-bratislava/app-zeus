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
        $tablePrefix = config('database.table_prefix');
        $inspectionTablePrefix = config('pkg-inspections.table_prefix');

        // inspectable subjects
        Schema::create($tablePrefix . 'inspection_assignments', function (Blueprint $table) use ($inspectionTablePrefix) {
            $table->id();
            $table->foreignId('inspection_id')
                ->nullable(false)
                ->comment('')
                ->constrained($inspectionTablePrefix . 'inspections');
            $table->unsignedBigInteger('subject_id')
                ->nullable(false)
                ->comment('');
            $table->string('subject_type')
                ->nullable(false)
                ->comment('');
            $table->timestamps();
            $table->softDeletes();
        });

        // inspectable subjects
        Schema::create($tablePrefix . 'inspection_templatables', function (Blueprint $table) use ($tablePrefix, $inspectionTablePrefix) {
            $table->id();
            $table->foreignId('template_id')
                ->nullable()
                ->comment('')
                ->constrained($inspectionTablePrefix . 'inspection_templates', 'id');
            $table->unsignedBigInteger('subject_id')
                ->nullable()
                ->comment('Single subject bound to this inspection template. E.g. vehicle model, ...');
            $table->string('subject_type')
                ->nullable()
                ->comment("Class of related polymorphic record. Determines respective database table holding records of this type.");

            $table->timestamps();
            $table->softDeletes();
        });          
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('database.table_prefix');

        Schema::dropIfExists($tablePrefix . 'inspection_assignments');
        Schema::dropIfExists($tablePrefix . 'inspection_templatables');
    }
};
