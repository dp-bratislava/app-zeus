<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use function Laravel\Prompts\table;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // transport groups
        Schema::create('dpb_fleet_transport_groups', function (Blueprint $table) {
            $table->comment('List of vehicle tarnsport groups');
            $table->id();
            $table->string('title');
            $table->string('short_title');
            $table->timestamps();
            $table->softDeletes();
        });

        // service groups
        Schema::create('dpb_fleet_service_groups', function (Blueprint $table) {
            $table->comment('List of vehicle service groups');
            $table->id();
            $table->string('title');
            $table->string('short_title');
            $table->timestamps();
            $table->softDeletes();
        });

        // vehicle types
        Schema::create('dpb_fleet_vehicle_types', function (Blueprint $table) {
            $table->comment('List of vehicle types');
            $table->id();
            $table->string('code');
            $table->string('title');
            $table->timestamps();
            $table->softDeletes();
        });

        // vehicle statuses
        Schema::create('dpb_fleet_vehicle_statuses', function (Blueprint $table) {
            $table->comment('List of vehicle statuses');
            $table->id();
            $table->string('code');
            $table->string('title');
            $table->timestamps();
            $table->softDeletes();
        });

        // vehicle groups
        Schema::create('dpb_fleet_vehicle_groups', function (Blueprint $table) {
            $table->comment('List of vehicle groups');
            $table->id();
            $table->string('code')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('responsible_id')
                ->nullable()
                ->comment('Person responsible for this group.')
                ->constrained('datahub_employees', 'id');
            $table->timestamps();
            $table->softDeletes();
        });

        // vehicle models
        Schema::create('dpb_fleet_vehicle_models', function (Blueprint $table) {
            $table->comment('List of vehicle models');
            $table->id();
            $table->string('title');
            $table->decimal('length')
                ->nullable()
                ->comment('Vehicle length in meters');
            $table->integer('warranty')
                ->nullable()
                ->comment('Default warranty in months');
            $table->foreignId('type_id')
                ->nullable(false)
                ->constrained('dpb_fleet_vehicle_types', 'id');
            $table->timestamps();
            $table->softDeletes();
        });

        // vehicles
        Schema::create('dpb_fleet_vehicles', function (Blueprint $table) {
            $table->comment('List of vehicles');
            $table->id();
            $table->string('code')
                ->comment('4 digit vehicle code. Multiple vehicles can have same code over time.');
            $table->string('licence_plate')
                ->nullable();
            $table->date('end_of_warranty')
                ->nullable();
            $table->foreignId('model_id')
                ->nullable(false)
                ->constrained('dpb_fleet_vehicle_models', 'id');
            $table->timestamps();
            $table->softDeletes();
        });

        // vehicle status pivot
        Schema::create('dpb_fleet_vehicle_status', function (Blueprint $table) {
            $table->comment('Pivot to bind vehicles and statuses');
            $table->id();
            $table->date('date');
            $table->foreignId('vehicle_id')
                ->nullable(false)
                ->constrained('dpb_fleet_vehicles', 'id');
            $table->foreignId('status_id')
                ->nullable(false)
                ->constrained('dpb_fleet_vehicle_statuses', 'id');
            $table->text('note');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dpb_fleet_vehicle_status');
        Schema::dropIfExists('dpb_fleet_vehicles');
        Schema::dropIfExists('dpb_fleet_vehicle_groups');
        Schema::dropIfExists('dpb_fleet_vehicle_models');
        Schema::dropIfExists('dpb_fleet_vehicle_types');
        Schema::dropIfExists('dpb_fleet_vehicle_statuses');
        Schema::dropIfExists('dpb_fleet_service_groups');
        Schema::dropIfExists('dpb_fleet_transport_groups');
    }
};
