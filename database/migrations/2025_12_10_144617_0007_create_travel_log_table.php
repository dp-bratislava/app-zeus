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

        // distance logs
        Schema::create($tablePrefix . 'travel_log', function (Blueprint $table) use ($tablePrefix) {
            $table->comment('List of vehicle distance travelled');
            $table->id();
            $table->date('date')
                ->nullable(false);
            $table->foreignId('vehicle_id')
                ->nullable(false)
                ->comment('')
                ->constrained($tablePrefix . 'vehicles', 'id');
            $table->float('distance')                
                ->nullable(false)
                ->comment('Distance traveled in km. Might have multiple records per day.');
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

        Schema::dropIfExists($tablePrefix . 'travel_log');
    }
};
