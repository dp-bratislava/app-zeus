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

        // vehicle codes
        Schema::table($tablePrefix . 'vehicle_models', function (Blueprint $table) {
            $table->dropColumn([
                'length',
                'tank_size',
                'seats'
            ]);
            $table->dropConstrainedForeignId('fuel_type_id');
            $table->dropConstrainedForeignId('alternate_fuel_type_id');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-fleet.table_prefix');

        Schema::table($tablePrefix . 'vehicle_models', function (Blueprint $table) use ($tablePrefix) {
            $table->decimal('length')->nullable()->comment('Length in meters');
            $table->integer('tank_size')->nullable()->comment('Tank size in liters');
            $table->integer('seats')->nullable()->comment('Number of seats in vehicle');
            $table->foreignId('fuel_type_id')
                ->nullable()
                ->constrained($tablePrefix . 'fuel_types', 'id');
            $table->foreignId('alternate_fuel_type_id')
                ->nullable()
                ->constrained($tablePrefix . 'fuel_types', 'id');
        });
    }
};
