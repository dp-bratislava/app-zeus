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

        // inspection template groups
        Schema::table($tablePrefix . 'inspection_templates', function (Blueprint $table) {
            $table->integer('treshold_distance')->nullable()->after('is_periodic')->comment('');
            $table->integer('first_advance_distance')->nullable()->after('treshold_distance')->comment('');
            $table->integer('second_advance_distance')->nullable()->after('first_advance_distance')->comment('');
            $table->integer('treshold_time')->nullable()->after('second_advance_distance')->comment('');
            $table->integer('first_advance_time')->nullable()->after('treshold_time')->comment('');
            $table->integer('second_advance_time')->nullable()->after('first_advance_time')->comment('');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-inspections.table_prefix');

        Schema::table($tablePrefix . 'inspection_templates', function (Blueprint $table) {
            $table->dropColumn([
                'treshold_distance',
                'first_advance_distance',
                'second_advance_distance',
                'treshold_time',
                'first_advance_time',
                'second_advance_time',
            ]);
        });
    }
};
