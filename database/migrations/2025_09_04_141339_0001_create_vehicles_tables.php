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

        // fuel types
        Schema::create($tablePrefix . 'fuel_types', function (Blueprint $table) {
            $table->comment('List of fuel types');
            $table->id();
            $table->string('code')
                ->nullable(false)
                ->unique()
                ->comment('Unique code of fuel type');
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // vehicle statuses
        Schema::create($tablePrefix . 'vehicle_statuses', function (Blueprint $table) {
            $table->comment('List of vehicle statuses');
            $table->id();
            $table->string('code')
                ->nullable(false)
                ->unique()
                ->comment('Unique code of vehicle status');
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // vehicle types
        Schema::create($tablePrefix . 'vehicle_types', function (Blueprint $table) {
            $table->comment('List of vehicle types');
            $table->id();
            $table->string('code');
            $table->string('title');
            $table->timestamps();
            $table->softDeletes();
        });

        // vehicle groups
        Schema::create($tablePrefix . 'vehicle_groups', function (Blueprint $table) {
            $table->comment('List of vehicle groups');
            $table->id();
            $table->string('code')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // licence plates
        Schema::create($tablePrefix . 'licence_plates', function (Blueprint $table) {
            $table->comment('List of vehicle licence plates');
            $table->id();
            $table->string('code')->nullable(false)->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        // vehicle models
        Schema::create($tablePrefix . 'vehicle_models', function (Blueprint $table) use ($tablePrefix) {
            $table->comment('List of vehicle models');
            $table->id();
            $table->string('title');
            $table->integer('warranty')
                ->nullable()
                ->comment('Default warranty in months');
            $table->foreignId('type_id')
                ->nullable(true)
                ->constrained($tablePrefix . 'vehicle_types', 'id');
            $table->string('year')->nullable(true);
            $table->decimal('length')->nullable(true)->comment('Length in meters');
            $table->integer('tank_size')->nullable(true)->comment('Tank size in liters');
            $table->integer('seats')->nullable(true)->comment('Number of seats in vehicle');
            $table->foreignId('fuel_type_id')
                ->nullable(true)
                ->constrained($tablePrefix . 'fuel_types', 'id');
            $table->foreignId('alternate_fuel_type_id')
                ->nullable(true)
                ->constrained($tablePrefix . 'fuel_types', 'id');
            $table->decimal('fuel_consumption')->nullable(true)->comment('Fuel consumption out of city');
            $table->decimal('fuel_consumption_city')->nullable(true)->comment('Fuel consumption out of city');
            $table->decimal('fuel_consumption_combined')->nullable(true)->comment('Fuel consumption out of city');
            $table->decimal('std_fuel_consumption_winter')->nullable(true)->comment('Standardised fuel consumption out of city in winter');
            $table->decimal('std_fuel_consumption_city_winter')->nullable(true)->comment('Standardised fuel consumption out of city in winter');
            $table->decimal('std_fuel_consumption_summer')->nullable(true)->comment('Standardised fuel consumption out of city in summer');
            $table->decimal('std_fuel_consumption_city_summer')->nullable(true)->comment('Standardised fuel consumption out of city in summer');

            $table->timestamps();
            $table->softDeletes();
        });

        // vehicles
        Schema::create($tablePrefix . 'vehicles', function (Blueprint $table) use ($tablePrefix) {
            $table->comment('List of vehicles');
            $table->id();
            $table->string('vin')
                ->nullable()
                ->comment('Unique VIN. not always stored.')
                ->unique();
            $table->string('code')
                ->nullable()
                ->comment('4 digit vehicle code. Multiple vehicles can have same code over time.');
            $table->foreignId('model_id')
                ->nullable(false)
                ->constrained($tablePrefix . 'vehicle_models', 'id');
            $table->timestamps();
            $table->softDeletes();
        });

        // vehicle licence plates history
        Schema::create($tablePrefix . 'licence_plate_history', function (Blueprint $table) use ($tablePrefix) {
            $table->comment('History of licence plate assignments to vehicles');
            $table->id();
            $table->foreignId('vehicle_id')
                ->nullable(false)
                ->constrained($tablePrefix . 'vehicles', 'id');
            $table->foreignId('licence_plate_id')
                ->nullable(false)
                ->constrained($tablePrefix . 'licence_plates', 'id');
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // vehicle statuses history
        Schema::create($tablePrefix . 'status_history', function (Blueprint $table) use ($tablePrefix) {
            $table->comment('History of vehicle statuses');
            $table->id();
            $table->foreignId('vehicle_id')
                ->nullable(false)
                ->constrained($tablePrefix . 'vehicles', 'id');
            $table->foreignId('status_id')
                ->nullable(false)
                ->constrained($tablePrefix . 'vehicle_statuses', 'id');
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // pivot vehicle groups
        Schema::create($tablePrefix . 'group_vehicle', function (Blueprint $table) use ($tablePrefix) {
            $table->comment('Pivot for vehicle group relation');
            $table->id();
            $table->foreignId('vehicle_id')
                ->nullable(false)
                ->constrained($tablePrefix . 'vehicles', 'id');
            $table->foreignId('group_id')
                ->nullable(false)
                ->constrained($tablePrefix . 'vehicle_groups', 'id');
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

        Schema::dropIfExists($tablePrefix . 'status_history');
        Schema::dropIfExists($tablePrefix . 'licence_plate_history');
        Schema::dropIfExists($tablePrefix . 'group_vehicle');
        Schema::dropIfExists($tablePrefix . 'vehicles');
        Schema::dropIfExists($tablePrefix . 'licence_plates');
        Schema::dropIfExists($tablePrefix . 'vehicle_statuses');
        Schema::dropIfExists($tablePrefix . 'vehicle_models');
        Schema::dropIfExists($tablePrefix . 'vehicle_types');
        Schema::dropIfExists($tablePrefix . 'vehicle_groups');
        Schema::dropIfExists($tablePrefix . 'fuel_types');
    }
};
