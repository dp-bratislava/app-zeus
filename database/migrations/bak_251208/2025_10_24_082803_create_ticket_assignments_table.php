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
        $ticketTablePrefix = config('pkg-tickets.table_prefix');

        // ticket bindings
        Schema::create($tablePrefix . 'ticket_assignments', function (Blueprint $table) use ($ticketTablePrefix) {
            $table->comment("Relations binding ticket to other domains");
            $table->id();
            $table->foreignId('ticket_id')
                ->nullable(false)
                ->comment('')
                ->constrained($ticketTablePrefix . 'tickets', 'id');
            // subject
            $table->unsignedBigInteger('subject_id')
                ->nullable()
                ->comment('Single subject bound to this ticket. E.g. vehicle, building, ...');
            $table->string('subject_type')
                ->nullable()
                ->comment("Class of related polymorphic record. Determines respective database table holding records of this type.");
            // source
            $table->unsignedBigInteger('source_id')
                ->nullable(false)
                ->comment('Source of ticket. E.g. daily maintenance, crash report, manual ...');
            $table->string('source_type')
                ->nullable(false)
                ->comment("Class of related polymorphic record. Determines respective database table holding records of this type.");
            // department
            $table->foreignId('department_id')
                ->nullable()
                ->comment('Department this ticket will be hadled for.')
                ->constrained('datahub_departments');
            $table->foreignId('author_id')
                ->nullable(false)
                ->comment('User that created ticket')
                ->constrained('users');
            $table->foreignId('assigned_to')
                ->nullable()
                ->comment("User responsible for ticket")
                ->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        // add indexes for polymorphic relations
        Schema::table($tablePrefix . 'ticket_assignments', function (Blueprint $table) use ($ticketTablePrefix) {
            $table->index(['source_type', 'source_id']);
            $table->index(['subject_type', 'subject_id']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('database.table_prefix');

        Schema::dropIfExists($tablePrefix . 'ticket_assignments');
    }
};
