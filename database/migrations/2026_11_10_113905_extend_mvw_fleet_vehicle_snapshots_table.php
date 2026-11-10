<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mvw_fleet_vehicle_snapshots', function (Blueprint $table) {
            $table->string('brand', 255)->nullable()->after('type');
            $table->decimal('length', 8, 2)->nullable()->after('brand')->comment('Length in meters');
            $table->integer('seats')->nullable()->after('length')->comment('Number of seats in vehicle');
            $table->boolean('is_historic')->nullable()->after('seats');
            $table->string('maintenance_group_title')->nullable()->after('is_historic');
            $table->string('maintenance_group_short_title')->nullable()->after('maintenance_group_title');
            $table->string('cost_department_code')->nullable()->after('maintenance_group_short_title');
            $table->string('cost_department_title')->nullable()->after('cost_department_code');
            $table->unsignedBigInteger('maintenance_group_id')->nullable()->after('cost_department_title');
            $table->unsignedBigInteger('cost_department_id')->nullable()->after('maintenance_group_id');
            $table->unsignedBigInteger('model_id')->nullable()->after('cost_department_id');
            $table->unsignedBigInteger('type_id')->nullable()->after('model_id');

            // indexes
            $table->index('is_historic', 'idx_is_historic');
            $table->index('maintenance_group_id', 'idx_maintenance_group_id');
            $table->index('cost_department_id', 'idx_cost_department_id');
            $table->index('cost_department_code', 'idx_cost_department_code');
            $table->index('model_id', 'idx_model_id');
            $table->index('type_id', 'idx_type_id');
        });


    }

    public function down(): void
    {
        Schema::table('mvw_fleet_vehicle_snapshots', function (Blueprint $table) {
            $table->dropColumn([
                'brand',
                'length',
                'seats',
                'is_historic',
                'maintenance_group_title',
                'maintenance_group_short_title',
                'cost_department_code',
                'cost_department_title',
                'maintenance_group_id',
                'cost_department_id',
                'model_id',
                'type_id',
            ]);
        });
    }
};
