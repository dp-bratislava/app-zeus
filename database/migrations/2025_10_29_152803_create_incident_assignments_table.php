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
        $incidentTablePrefix = config('pkg-incidents.table_prefix');

        // incident bindings
        Schema::create($tablePrefix . 'incident_assignments', function (Blueprint $table) use ($incidentTablePrefix) {
            $table->comment("Relations binding incident to other domains");
            $table->id();
            $table->foreignId('incident_id')
                ->nullable(false)
                ->comment('')
                ->constrained($incidentTablePrefix . 'incidents', 'id');
            // subject
            $table->unsignedBigInteger('subject_id')
                ->nullable()
                ->comment('Single subject bound to this incident. E.g. vehicle, building, ...');
            $table->string('subject_type')
                ->nullable()
                ->comment("Class of related polymorphic record. Determines respective database table holding records of this type.");
            $table->foreignId('author_id')
                ->nullable(false)
                ->comment('User that created incident')
                ->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        // add indexes for polymorphic relations
        Schema::table($tablePrefix . 'incident_assignments', function (Blueprint $table) use ($incidentTablePrefix) {
            $table->index(['subject_type', 'subject_id']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('database.table_prefix');

        Schema::dropIfExists($tablePrefix . 'incident_assignments');
    }
};
