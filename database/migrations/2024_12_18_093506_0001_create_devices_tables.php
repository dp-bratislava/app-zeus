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
        $tablePrefix = config('pkg-devices.table_prefix');

        // device types
        Schema::create($tablePrefix . 'device_types', function (Blueprint $table) {
            $table->comment('List of device types.');
            $table->id();
            $table->string('code');
            $table->string('title');
            $table->timestamps();
            $table->softDeletes();
        });

        // device groups
        Schema::create($tablePrefix . 'device_groups', function (Blueprint $table) {
            $table->comment('List of device groups');
            $table->id();
            $table->string('code')->nullable()->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // device locations
        Schema::create($tablePrefix . 'device_locations', function (Blueprint $table) {
            $table->comment('List of device locations');
            $table->id();
            $table->string('title')->nullable(false)->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        // device models
        Schema::create($tablePrefix . 'device_models', function (Blueprint $table) use ($tablePrefix) {
            $table->comment('List of device models');
            $table->id();
            $table->string('title');
            $table->foreignId('type_id')
                ->nullable()
                ->constrained($tablePrefix . 'device_types', 'id');
            $table->timestamps();
            $table->softDeletes();
        });

        // devices
        Schema::create($tablePrefix . 'devices', function (Blueprint $table) use ($tablePrefix) {
            $table->comment('List of devices');
            $table->id();
            $table->string('serial_number')
                ->nullable()
                ->comment('Unique SN. not always stored.')
                ->unique();
            $table->string('location');
            $table->foreignId('model_id')
                ->nullable()
                ->constrained($tablePrefix . 'device_models', 'id');
            $table->timestamps();
            $table->softDeletes();
        });

        // device locations history
        Schema::create($tablePrefix . 'device_location_history', function (Blueprint $table) use ($tablePrefix) {
            $table->comment('History of location assignments to devices');
            $table->id();
            $table->foreignId('device_id')
                ->nullable(false)
                ->constrained($tablePrefix . 'devices', 'id');
            $table->foreignId('location_id')
                ->nullable(false)
                ->constrained($tablePrefix . 'device_locations', 'id');
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // pivot device groups
        Schema::create($tablePrefix . 'device_group', function (Blueprint $table) use ($tablePrefix) {
            $table->comment('Pivot for device group relation');
            $table->id();
            $table->foreignId('device_id')
                ->nullable(false)
                ->constrained($tablePrefix . 'devices', 'id');
            $table->foreignId('group_id')
                ->nullable(false)
                ->constrained($tablePrefix . 'device_groups', 'id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-devices.table_prefix');

        Schema::dropIfExists($tablePrefix . 'device_location_history');
        Schema::dropIfExists($tablePrefix . 'device_group');
        Schema::dropIfExists($tablePrefix . 'devices');
        Schema::dropIfExists($tablePrefix . 'device_locations');
        Schema::dropIfExists($tablePrefix . 'device_models');
        Schema::dropIfExists($tablePrefix . 'device_types');
        Schema::dropIfExists($tablePrefix . 'device_groups');
    }
};
