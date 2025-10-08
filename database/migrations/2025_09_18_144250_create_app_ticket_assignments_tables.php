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

        // ticket subject pivot
        Schema::create($tablePrefix . 'ticket_subjects', function (Blueprint $table) use ($ticketTablePrefix) {
            $table->comment("Pivot to bind ticket to it's subject. e.g. vehicle, building, etc");
            $table->id();
            $table->foreignId('ticket_id')
                ->nullable(false)
                ->comment('')
                ->constrained($ticketTablePrefix . 'tickets', 'id');
            $table->unsignedBigInteger('subject_id')
                ->nullable(false)
                ->comment('Single subject bound to this ticket. E.g. vehicle, building, ...');
            $table->string('subject_type')
                ->nullable(false)
                ->comment("Class of related polymorphic record. Determines respective database table holding records of this type.");

            $table->timestamps();
            $table->softDeletes();
        });

        // ticket header pivot
        Schema::create($tablePrefix . 'ticket_headers', function (Blueprint $table) use ($ticketTablePrefix) {
            $table->comment("Relations binding ticket to other domains");
            $table->id();
            $table->foreignId('ticket_id')
                ->nullable(false)
                ->comment('')
                ->constrained($ticketTablePrefix . 'tickets', 'id');
            $table->foreignId('department_id')
                ->nullable(false)
                ->comment('Department this ticket will be hadled for.')
                ->constrained('datahub_departments');
            $table->foreignId('author_id')
                ->nullable(false)
                ->comment('')
                ->constrained('datahub_employee_contracts');
            $table->string('assigned_to')
                ->nullable(false)
                ->comment("")
                ->constrained('users');
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

        Schema::dropIfExists($tablePrefix . 'ticket_subjects');
        Schema::dropIfExists($tablePrefix . 'ticket_headers');
    }
};
