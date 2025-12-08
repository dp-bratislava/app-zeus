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
        $tablePrefix = config('pkg-work-log.table_prefix');

        // work interval
        Schema::create($tablePrefix . 'work_intervals', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();

            $table->dateTime('time_from')->nullable();
            $table->dateTime('time_to')->nullable();
            $table->text('description')->nullable();
            // computed column with duration in minutes
            $table->integer('duration')
                ->nullable()
                ->storedAs('TIMESTAMPDIFF(MINUTE, time_from, time_to)')
                ->comment('Computed column with real duration in minutes');
            $table->text('note')
                ->nullable()
                ->comment('Optional note');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-work-log.table_prefix');

        Schema::dropIfExists($tablePrefix . 'work_intervals');
    }
};
