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

        // add warranty to vehicles
        Schema::table($tablePrefix . 'vehicles', function (Blueprint $table) use ($tablePrefix) {
            $table->integer('construction_year')
                ->nullable()
                ->comment('May differ from model year')
                ->after('maintenance_group_id');  
            $table->integer('comissioning_date')
                ->nullable()
                ->comment('Date when we added this vehicle to fleet')
                ->after('construction_year');            
            $table->date('warranty_initial_date')
                ->nullable()
                ->comment('Warranty runs from this date')
                ->after('construction_year');
            $table->integer('warranty_months')
                ->nullable()
                ->comment('Number of months the warranty stands after warranty_intial_date')
                ->after('warranty_initial_date');
            $table->integer('warranty_initial_km')
                ->nullable()
                ->comment('Warranty runs from this value of km travelled')
                ->after('warranty_months');
            $table->integer('warranty_km')
                ->nullable()
                ->comment('Number of km the warranty stands after warranty_intial_km')
                ->after('warranty_initial_km');
        });

        // copy data if needed

        // drop warranty from vehicle models
        Schema::table($tablePrefix . 'vehicle_models', function (Blueprint $table) use ($tablePrefix) {
            $table->dropColumn('warranty');
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-fleet.table_prefix');

        // drop warranty from vehicles        
        Schema::table($tablePrefix . 'vehicles', function (Blueprint $table) use ($tablePrefix) {
            $table->dropColumn([
                'construction_year',
                'warranty_initial_date',
                'warranty_months',
                'warranty_initial_km',
                'warranty_km',
            ]);
        });

        // copy data if needed

        // add warranty to vehicle models
        Schema::table($tablePrefix . 'vehicle_models', function (Blueprint $table) use ($tablePrefix) {
            $table->integer('warranty')
                ->nullable()
                ->comment('Default warranty in months');
        }); 
    }
};
