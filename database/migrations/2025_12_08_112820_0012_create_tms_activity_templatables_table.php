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

        $tablePrefix = config('pkg-task-ms.table_prefix');
        $activityTablePrefix = config('pkg-activities.table_prefix');

        // inspectable templatables
        Schema::create($tablePrefix . 'activity_templatables', function (Blueprint $table) use ($tablePrefix, $activityTablePrefix) {
            $table->comment('List of activity templates available for given templatable.');
            
            $table->id();
            $table->foreignId('template_id')
                ->nullable()
                ->comment('')
                ->constrained($activityTablePrefix . 'activity_templates', 'id');
            $table->unsignedBigInteger('templatable_id')
                ->nullable()
                ->comment('Single templatable bound to this activiy template. E.g. vehicle model, ...');
            $table->string('templatable_type')
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
        $tablePrefix = config('pkg-task-ms.table_prefix');

        Schema::dropIfExists($tablePrefix . 'activity_templatables');
    }
};
