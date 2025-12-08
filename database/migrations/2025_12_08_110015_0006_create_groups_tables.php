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

        // vehicle maintenance groups
        Schema::create($tablePrefix . 'maintenance_groups', function (Blueprint $table) {
            $table->comment('List of vehicle maintenance groups');
            $table->id();
            $table->string('code');
            $table->string('title')
                ->nullable(false)
                ->unique()
                ->comment('Unique vehicle maintenance group title');
            $table->string('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // vehicle dispatch groups
        Schema::create($tablePrefix . 'dispatch_groups', function (Blueprint $table) {
            $table->comment('List of vehicle dispatch groups');
            $table->id();
            $table->string('code');
            $table->string('title')
                ->nullable(false)
                ->unique()
                ->comment('Unique vehicle dispatch group title');
            $table->string('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // pivot vehicle dispatch groups
        Schema::create($tablePrefix . 'dispatch_group_vehicle', function (Blueprint $table) use ($tablePrefix) {
            $table->comment('Pivot for vehicle dispatch groups');
            $table->id();
            $table->foreignId('vehicle_id')
                ->nullable()
                ->constrained($tablePrefix . 'vehicles', 'id');
            $table->foreignId('group_id')
                ->nullable()
                ->constrained($tablePrefix . 'dispatch_groups', 'id');
            $table->date('date_from')->nullable(false);
            $table->date('date_to')->nullable();
        });

        // pivot vehicle maintenance groups
        Schema::create($tablePrefix . 'maintenance_group_vehicle', function (Blueprint $table) use ($tablePrefix) {
            $table->comment('Pivot for vehicle maintenance groups');
            $table->id();
            $table->foreignId('vehicle_id')
                ->nullable()
                ->constrained($tablePrefix . 'vehicles', 'id');
            $table->foreignId('group_id')
                ->nullable()
                ->constrained($tablePrefix . 'maintenance_groups', 'id');
            $table->date('date_from')->nullable(false);
            $table->date('date_to')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-fleet.table_prefix');

        Schema::dropIfExists($tablePrefix . 'dispatch_group_vehicle');
        Schema::dropIfExists($tablePrefix . 'maintenance_group_vehicle');
        Schema::dropIfExists($tablePrefix . 'dispatch_groups');
        Schema::dropIfExists($tablePrefix . 'maintenance_groups');
    }
};
