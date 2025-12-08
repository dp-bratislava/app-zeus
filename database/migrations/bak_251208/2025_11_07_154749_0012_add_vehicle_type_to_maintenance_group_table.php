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

        // add warranty to vehicles
        Schema::table($tablePrefix . 'maintenance_groups', function (Blueprint $table) use ($tablePrefix) {
           $table->foreignId('vehicle_type_id')
                ->nullable()
                ->comment('Vehicle type handled by this group')
                ->after('color')
                ->constrained($tablePrefix . 'vehicle_types', 'id');

        });     
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-fleet.table_prefix');

        // drop warranty from vehicles        
        Schema::table($tablePrefix . 'vehicles', function (Blueprint $table) use ($tablePrefix) {
            $table->dropConstrainedForeignId('vehicle_type_id');
        });
    }
};
