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
        Schema::table($tablePrefix . 'ticket_assignments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('assigned_to');
            // entity responsible e.g. maintenance group, department, ...
            $table->unsignedBigInteger('assigned_to_id')
                ->nullable()
                ->after('author_id')
                ->comment('Entity responsible for this ticket item e.g. maintenance group, department, ...');
            $table->string('assigned_to_type')
                ->nullable()
                ->after('assigned_to_id')
                ->comment("Class of related polymorphic record. Determines respective database table holding records of this type.");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('database.table_prefix');
        Schema::table($tablePrefix . 'ticket_assignments', function (Blueprint $table) {
            $table->foreignId('assigned_to')
                ->nullable()
                ->comment("User responsible for ticket")
                ->constrained('users');
            $table->dropColumn([
                'assigned_to_id',
                'assigned_to_type'
            ]);
        });
    }
};
