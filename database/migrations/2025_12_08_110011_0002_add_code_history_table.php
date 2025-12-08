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
        Schema::create($tablePrefix . 'vehicle_codes', function (Blueprint $table) {
            $table->comment('List of vehicle codes');
            $table->id();
            $table->string('code')->nullable(false)->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        // vehicle codes history
        Schema::create($tablePrefix . 'vehicle_code_history', function (Blueprint $table) use ($tablePrefix) {
            $table->comment('History of vehicle code assignments to vehicles');
            $table->id();
            $table->foreignId('vehicle_id')
                ->nullable(false)
                ->constrained($tablePrefix . 'vehicles', 'id');
            $table->foreignId('vehicle_code_id')
                ->nullable(false)
                ->constrained($tablePrefix . 'vehicle_codes', 'id');
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-fleet.table_prefix');

        Schema::dropIfExists($tablePrefix . 'vehicle_code_history');
        Schema::dropIfExists($tablePrefix . 'vehicle_codes');
    }
};
