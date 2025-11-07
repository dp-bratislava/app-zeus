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

        // brands
        Schema::create($tablePrefix . 'brands', function (Blueprint $table) {
            $table->comment('List of vehicle brands');
            $table->id();
            $table->string('title')
                ->nullable(false)
                ->unique()
                ->comment('Unique title of brand');
            $table->timestamps();
            $table->softDeletes();
        });

        // add brand to vehicle model
        Schema::table($tablePrefix . 'vehicle_models', function (Blueprint $table) use ($tablePrefix) {
            $table->foreignId('brand_id')
                ->nullable()
                ->comment('Vehicle model brand')
                ->after('title')
                ->constrained($tablePrefix . 'brands', 'id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-fleet.table_prefix');

        Schema::table($tablePrefix . 'vehicle_models', function (Blueprint $table) {
            $table->dropConstrainedForeignId('brand_id');
        });
        Schema::dropIfExists($tablePrefix . 'brands');
    }
};
