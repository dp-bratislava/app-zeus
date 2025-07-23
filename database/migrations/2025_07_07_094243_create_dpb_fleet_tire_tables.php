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
        // // TO DO maybe move to datahub and pull from SAP suppliers
        // Schema::create('dpb_suppliers', function (Blueprint $table) {
        //     $table->comment('List of suppliers');
        //     $table->id();
        //     $table->string('title');
        //     $table->string('short_title');
        //     $table->timestamps();
        //     $table->softDeletes();
        // });

        // tire brands
        Schema::create('dpb_fleet_tire_brands', function (Blueprint $table) {
            $table->comment('List of tire brands');
            $table->id();
            $table->string('code');
            $table->string('title');
            $table->timestamps();
            $table->softDeletes();
        });

        // tire seasons
        Schema::create('dpb_fleet_tire_seasons', function (Blueprint $table) {
            $table->comment('List of tire seasons');
            $table->id();
            $table->string('code');
            $table->string('title');
            $table->timestamps();
            $table->softDeletes();
        });

        // tire statuses
        Schema::create('dpb_fleet_tire_statuses', function (Blueprint $table) {
            $table->comment('List of tire statuses');
            $table->id();
            $table->string('code');
            $table->string('title');
            $table->timestamps();
            $table->softDeletes();
        });

        // tire season
        Schema::create('dpb_fleet_tire_types', function (Blueprint $table) {
            $table->comment('List of vehicle types');
            $table->id();
            $table->string('code');
            $table->string('title');
            $table->timestamps();
            $table->softDeletes();
        });

        // tire construction types
        Schema::create('dpb_fleet_tire_construction_types', function (Blueprint $table) {
            $table->comment('List of tire construction types');
            $table->id();
            $table->string('code');
            $table->string('title');
            $table->timestamps();
            $table->softDeletes();
        });

        // tire parameters
        Schema::create('dpb_fleet_tire_parameters', function (Blueprint $table) {
            $table->comment('List of tire parameters');
            $table->id();
            $table->integer('tire_width')->comment('Tire width in mm');
            $table->integer('profile_number');
            $table->decimal('rim_diameter');
            $table->foreignId('construction_type_id')
                ->nullable()
                ->comment('')
                ->constrained('dpb_fleet_tire_construction_types', 'id');
            $table->string('load_index')
                ->comment('Maximal weight distributed on one tire.');
            $table->string('speed_rating')
                ->comment('maximum speed the tire can safely maintain over time under its recommended load.');
            $table->timestamps();
            $table->softDeletes();
        });

        // tires
        Schema::create('dpb_fleet_tires', function (Blueprint $table) {
            $table->comment('List of tires');
            $table->id();
            $table->string('code');
            $table->string('title');
            $table->text('description');
            $table->foreignId('brand_id')
                ->nullable()
                ->comment('')
                ->constrained('dpb_fleet_tire_brands', 'id');
            $table->foreignId('parameters_id')
                ->nullable()
                ->comment('')
                ->constrained('dpb_fleet_tire_parameters', 'id');
            $table->foreignId('status_id')
                ->nullable()
                ->comment('')
                ->constrained('dpb_fleet_tire_statuses', 'id');
            $table->integer('skeleton_status');
            $table->integer('lifespan')->comment('Lifespan in km');
            $table->integer('price')->comment('price.');
            $table->integer('rank')->comment('');
            $table->string('type')->comment('');
            $table->timestamps();
            $table->softDeletes();
        });

        // pivot vehicle tire
        Schema::create('dpb_fleet_vehicle_tire', function (Blueprint $table) {
            $table->comment('Pivot binding tire to vehicle');
            $table->id();
            $table->decimal('distance_traveled')
                ->nullable()
                ->comment('Distance traveled by this tire in km.');
            $table->date('date_from')
                ->comment('Date when tire was put on this vehicle');
            $table->date('date_to')
                ->comment('Date when tire was taken of this vehicle');
            $table->decimal('vehicle_distance_traveled_from')
                ->comment('Vehicle distance traveled when tire was put on.');
            $table->decimal('vehicle_distance_traveled_to')
                ->comment('Vehicle distance traveled when tire was taken off.');
            $table->foreignId('vehicle_id')
                ->nullable(false)
                ->constrained('dpb_fleet_vehicles', 'id');
            $table->foreignId('tire_id')
                ->nullable(false)
                ->constrained('dpb_fleet_tires', 'id');
            $table->integer('wheel_number')->comment('Number of wheel this tire is put on.');
            $table->text('note');
            $table->timestamps();
            $table->softDeletes();
        });

        // TO DO pivot tire vehicle group
        // vehicle group might be same thing as vehicle service group
        // Schema::create('dpb_fleet_tire_vehicle_group', function (Blueprint $table) {
        //     $table->comment('Pivot binding tire to vehicle group');
        //     $table->id();
        //     $table->date('date_from')
        //         ->comment('Date since when tire was bound to this vehicle group');
        //     $table->date('date_to')
        //         ->comment('Date until tire was removed from this vehicle group');
        //     $table->foreignId('tire_id')
        //         ->nullable(false)
        //         ->constrained('dpb_fleet_tires', 'id');
        //     $table->foreignId('vehicle_group_id')
        //         ->nullable(false)
        //         ->constrained('dpb_fleet_vehicle_groups', 'id');
        //     $table->timestamps();
        //     $table->softDeletes();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('dpb_fleet_tire_vehicle_group');
        Schema::dropIfExists('dpb_fleet_vehicle_tire');
        Schema::dropIfExists('dpb_fleet_tires');
        Schema::dropIfExists('dpb_fleet_tire_parameters');
        Schema::dropIfExists('dpb_fleet_tire_cosntruction_types');
        Schema::dropIfExists('dpb_fleet_tire_types');
        Schema::dropIfExists('dpb_fleet_tire_statuses');
        Schema::dropIfExists('dpb_fleet_tire_seasons');
        Schema::dropIfExists('dpb_fleet_tire_brands');
        // Schema::dropIfExists('dpb_suppliers');
    }
};
