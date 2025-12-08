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
        $tablePrefix = config('database.table_prefix');
        $fleetTablePrefix = config('pkg-fleet.table_prefix');

        Schema::create($tablePrefix . 'daily_expeditions', function (Blueprint $table) use ($fleetTablePrefix) {
            $table->comment('List of daily vehicle expedition');
            $table->id();
            $table->date('date')->nullable(false);
            $table->foreignId('vehicle_id')
                ->nullable(false)
                ->constrained($fleetTablePrefix . 'vehicles', 'id');
            $table->string('state')->nullable(false);
            $table->string('service')->nullable()->comment('Optional line, or service type');
            $table->string('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('database.table_prefix');
        Schema::dropIfExists($tablePrefix . 'daily_expeditions');
    }
};
