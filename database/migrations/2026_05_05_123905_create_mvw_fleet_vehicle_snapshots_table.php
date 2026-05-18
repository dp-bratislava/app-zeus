<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mvw_fleet_vehicle_snapshots', function (Blueprint $table) {
            $table->comment('Denormalised materialised view for fleet vehicle');

            $table->id();
            $table->unsignedBigInteger('vehicle_id');

            $table->string('code', 255)->nullable();
            $table->string('licence_plate', 255)->nullable();
            $table->string('model', 255)->nullable();
            $table->string('type', 255)->nullable();

            $table->timestamps();

            $table->index('updated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mvw_fleet_vehicle_snapshots');
    }
};