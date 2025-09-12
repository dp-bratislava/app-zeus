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

        // measurement units
        Schema::create($tablePrefix . 'measurement_units', function (Blueprint $table) {
            $table->id();

            $table->string('code')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        // inspection condition types
        Schema::create($tablePrefix . 'inspection_template_condition_types', function (Blueprint $table) use ($tablePrefix) {
            $table->id();

            $table->string('code')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('measurement_unit_id')
                ->nullable()
                ->comment('')
                ->constrained($tablePrefix . 'measurement_units', 'id', $tablePrefix . 'condition_unit_foreign');

            $table->timestamps();
            $table->softDeletes();
        });

        // inspection tempaltes
        Schema::create($tablePrefix . 'inspection_templates', function (Blueprint $table) {
            $table->id();

            $table->string('code')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_periodic')
                ->nullable(false)
                ->default(false);

            $table->timestamps();
            $table->softDeletes();
        });

        // inspection tempalte conditions
        Schema::create($tablePrefix . 'inspection_template_conditions', function (Blueprint $table) use ($tablePrefix) {
            $table->id();

            $table->string('code')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('template_id')
                ->nullable()
                ->comment('')
                ->constrained($tablePrefix . 'inspection_templates', 'id');
            $table->foreignId('condition_type_id')
                ->nullable()
                ->comment('')
                ->constrained($tablePrefix . 'inspection_template_condition_types', 'id');
            $table->float('value')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        // inspections
        Schema::create($tablePrefix . 'inspections', function (Blueprint $table) use ($tablePrefix) {
            $table->id();
            $table->date('date')->nullable();
            $table->foreignId('template_id')
                ->nullable()
                ->comment('')
                ->constrained($tablePrefix . 'inspection_templates', 'id');
            $table->unsignedBigInteger('subject_id')
                ->nullable()
                ->comment('Single subject bound to this inspection. E.g. vehicle, building, device, ...');
            $table->string('subject_type')
                ->nullable()
                ->comment("Class of related polymorphic record. Determines respective database table holding records of this type.");

            $table->timestamps();
            $table->softDeletes();
        });

        // inspectable subjects
        Schema::create($tablePrefix . 'inspection_templatables', function (Blueprint $table) use ($tablePrefix) {
            $table->id();
            $table->foreignId('template_id')
                ->nullable()
                ->comment('')
                ->constrained($tablePrefix . 'inspection_templates', 'id');
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
        $tablePrefix = config('pkg-inspections.table_prefix');

        Schema::dropIfExists($tablePrefix . 'inspectable_subjects');
        Schema::dropIfExists($tablePrefix . 'inspections');
        Schema::dropIfExists($tablePrefix . 'measurement_units');
        Schema::dropIfExists($tablePrefix . 'inspection_template_conditions');
        Schema::dropIfExists($tablePrefix . 'inspection_template_condition_types');
        Schema::dropIfExists($tablePrefix . 'inspection_templatables');
    }
};
