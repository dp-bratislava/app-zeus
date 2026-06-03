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
        Schema::create('dpb_sap_activity_types', function (Blueprint $table) {
            $table->comment('SAP activity types (Druh výkonu)');
            $table->id();

            $table->string('code')->nullable(false)->comment('');

            $table->unique([
                'code',
            ], 'sap_activity_type_unique');
        });

        Schema::create('dpb_sap_expense_types', function (Blueprint $table) {
            $table->comment('SAP expense types (Nákladový druh)');
            $table->id();

            $table->string('code')->nullable(false)->comment('');

            $table->unique([
                'code',
            ], 'sap_expense_type_unique');
        });

        Schema::create('dpb_sap_operation_categories', function (Blueprint $table) {
            $table->comment('SAP operation categories');
            $table->id();

            $table->string('code')->comment('Code');
            $table->string('title')->comment('');

            $table->unique([
                'code',
            ], 'sap_operation_category_unique');
        });

        Schema::create('dpb_sap_operations', function (Blueprint $table) {
            $table->comment('SAP operation with code');
            $table->id();

            $table->string('code')->comment('SAP code');
            $table->string('title')->comment('');
            $table->foreignId('category_id')
                ->comment('SAP operation category')
                ->constrained('dpb_sap_operation_categories', 'id')
                ->cascadeOnDelete();

            $table->unique([
                'code',
            ], 'sap_operation_code_unique');
        });

        Schema::create('dpb_sap_operation_pivot', function (Blueprint $table) {
            $table->comment('Pivot binding expense, operation, vehicle type and SAP codes');
            $table->id();

            $table->foreignId('activity_type_id')
                ->constrained('dpb_sap_activity_types', 'id')
                ->cascadeOnDelete();

            $table->foreignId('expense_type_id')
                ->constrained('dpb_sap_expense_types', 'id')
                ->cascadeOnDelete();

            $table->foreignId('operation_id')
                ->comment('SAP operation')
                ->constrained('dpb_sap_operations', 'id')
                ->cascadeOnDelete();

            $table->foreignId('vehicle_type_id')
                ->nullable(false)
                ->constrained('fleet_vehicle_types', 'id');

            // prevent duplicates (important for mapping tables)
            $table->unique([
                'activity_type_id',
                'expense_type_id',
                'operation_id',
            ], 'op_exp_sap_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dpb_sap_operation_pivot');
        Schema::dropIfExists('dpb_sap_operations');
        Schema::dropIfExists('dpb_sap_activity_types');
        Schema::dropIfExists('dpb_sap_expense_types');
        Schema::dropIfExists('dpb_sap_operation_categories');
    }
};
