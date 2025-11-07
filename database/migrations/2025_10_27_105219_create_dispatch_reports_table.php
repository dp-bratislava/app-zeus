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
        Schema::create($tablePrefix . 'dispatch_reports', function (Blueprint $table) use ($fleetTablePrefix) {
            $table->comment('WIP demo table for dispatch reports');
            $table->id();
            $table->date('date')->nullable();
            $table->foreignId('vehicle_id')
                ->nullable()
                ->constrained($fleetTablePrefix . 'vehicles', 'id');
            $table->text('description')->nullable();
            $table->foreignId('author_id')
                ->nullable()
                ->constrained('users', 'id');
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
        Schema::dropIfExists($tablePrefix . 'dispatch_reports');
    }
};
