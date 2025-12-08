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
        $activityTablePrefix = config('pkg-activities.table_prefix');

        // inspectable subjects
        Schema::create($tablePrefix . 'activity_templatables', function (Blueprint $table) use ($tablePrefix, $activityTablePrefix) {
            $table->comment('List of activity templates available for given subject.');
            
            $table->id();
            $table->foreignId('template_id')
                ->nullable()
                ->comment('')
                ->constrained($activityTablePrefix . 'activity_templates', 'id');
            $table->unsignedBigInteger('subject_id')
                ->nullable()
                ->comment('Single subject bound to this activiy template. E.g. vehicle model, ...');
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

        Schema::dropIfExists($tablePrefix . 'activity_templatables');
    }
};
