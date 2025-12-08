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

        Schema::create($tablePrefix . 'department_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')
                ->nullable(false)
                ->comment('')
                ->constrained('datahub_departments');
            $table->unsignedBigInteger('subject_id')
                ->nullable(false)
                ->comment('');
            $table->string('subject_type')
                ->nullable(false)
                ->comment('');
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

        Schema::dropIfExists($tablePrefix . 'department_assignments');
    }
};
