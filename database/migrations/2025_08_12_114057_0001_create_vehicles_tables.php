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
        $tablePrefix = config('pkg-vehicles.table_prefix');

        // vehicle types
        Schema::create($tablePrefix . 'vehicle_types', function (Blueprint $table) {
            $table->comment('List of vehicle types');
            $table->id();
            $table->string('code');
            $table->string('title');
            $table->timestamps();
            $table->softDeletes();
        });

        // // vehicle groups
        // Schema::create($tablePrefix . 'vehicle_groups', function (Blueprint $table) {
        //     $table->comment('List of vehicle groups');
        //     $table->id();
        //     $table->string('code')->nullable();
        //     $table->string('title');
        //     $table->text('description')->nullable();
        //     $table->timestamps();
        //     $table->softDeletes();
        // });

        // vehicle models
        Schema::create($tablePrefix . 'vehicle_models', function (Blueprint $table) use ($tablePrefix) {
            $table->comment('List of vehicle models');
            $table->id();
            $table->string('title');
            // $table->integer('warranty')
            //     ->nullable()
            //     ->comment('Default warranty in months');
            $table->foreignId('type_id')
                ->nullable(true)
                ->constrained($tablePrefix . 'vehicle_types', 'id');
            // $table->string('year')->nullable(true);
            // // $table->decimal('length')->nullable(true)->comment('Length in meters');
            // $table->integer('tank_size')->nullable(true)->comment('Tank size in liters');
            // $table->integer('seats')->nullable(true)->comment('Number of seats in vehicle');
            // $table->foreignId('fuel_type_id')
            //     ->nullable(true)
            //     ->constrained($tablePrefix . 'dpb_fleet_fuel_types', 'id');
            // $table->foreignId('alternate_fuel_type_id')
            //     ->nullable(true)
            //     ->constrained($tablePrefix . 'dpb_fleet_fuel_types', 'id');
            // $table->decimal('fuel_consumption')->nullable(true)->comment('Fuel consumption out of city');
            // $table->decimal('fuel_consumption_city')->nullable(true)->comment('Fuel consumption out of city');
            // $table->decimal('fuel_consumption_combined')->nullable(true)->comment('Fuel consumption out of city');
            // $table->decimal('std_fuel_consumption_winter')->nullable(true)->comment('Standardised fuel consumption out of city in winter');
            // $table->decimal('std_fuel_consumption_city_winter')->nullable(true)->comment('Standardised fuel consumption out of city in winter');
            // $table->decimal('std_fuel_consumption_summer')->nullable(true)->comment('Standardised fuel consumption out of city in summer');
            // $table->decimal('std_fuel_consumption_city_summer')->nullable(true)->comment('Standardised fuel consumption out of city in summer');

            $table->timestamps();
            $table->softDeletes();
        });

        // vehicles
        Schema::create($tablePrefix . 'vehicles', function (Blueprint $table) use ($tablePrefix) {
            $table->comment('List of vehicles');
            $table->id();
            $table->string('code')
                ->comment('4 digit vehicle code. Multiple vehicles can have same code over time.');
            $table->foreignId('model_id')
                ->nullable(false)
                ->constrained($tablePrefix . 'vehicle_models', 'id');
            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-vehicles.table_prefix');

        Schema::dropIfExists($tablePrefix . 'vehicles');
        Schema::dropIfExists($tablePrefix . 'vehicle_models');
        Schema::dropIfExists($tablePrefix . 'vehicle_types');
        Schema::dropIfExists($tablePrefix . 'vehicle_groups');
    }
};
