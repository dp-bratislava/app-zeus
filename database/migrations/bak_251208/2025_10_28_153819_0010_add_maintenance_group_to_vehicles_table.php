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
        $tablePrefix = config('pkg-fleet.table_prefix');

        // maintenance group colors
        Schema::table($tablePrefix . 'vehicles', function (Blueprint $table) use ($tablePrefix) {
            $table->foreignId('maintenance_group_id')
                ->nullable()
                ->after('model_id')
                ->constrained($tablePrefix . 'maintenance_groups', 'id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-fleet.table_prefix');

        Schema::table($tablePrefix . 'vehicles', function (Blueprint $table) use ($tablePrefix) {
            $table->dropConstrainedForeignId('maintenance_group_id');
        });
    }
};
