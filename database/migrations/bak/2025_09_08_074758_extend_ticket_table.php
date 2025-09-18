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
        $tablePrefix = config('pkg-tickets.table_prefix');

        // // ticket subjects
        // Schema::create($tablePrefix . 'ticket_subjects', function (Blueprint $table) {
        //     $table->comment('List of ticket subjects');
        //     $table->id();
        //     $table->unsignedBigInteger('subject_id')
        //         ->nullable(false)                
        //         ->comment("ID of related polymorphic record in it's respective table. FK to detail table.");
        //     $table->string('subject_type')
        //         ->nullable(false)
        //         ->comment("Class of related polymorphic record. Determines respective database table holding records of this type.");
        //     $table->timestamps();
        //     $table->softDeletes();
        // });

        // ticket subject
        Schema::table($tablePrefix . 'tickets', function (Blueprint $table) {
            $table->unsignedBigInteger('subject_id')
                ->nullable()
                ->comment('Single subject bound to this ticket. E.g. vehicle, building, device, ...');
            $table->string('subject_type')
                ->nullable()
                ->comment("Class of related polymorphic record. Determines respective database table holding records of this type.");
            $table->foreignId('department_id')
                ->nullable()
                ->comment("Department handling this ticket.")
                ->constrained('datahub_departments', 'id');
            $table->string('state')
                ->nullable()
                ->comment("Current ticket state");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-tickets.table_prefix');

        Schema::table($tablePrefix . 'tickets', function (Blueprint $table) {
            $table->dropColumn('subject_id');
            $table->dropColumn('subject_type');
            $table->dropConstrainedForeignId('department_id');
        });
    }
};
