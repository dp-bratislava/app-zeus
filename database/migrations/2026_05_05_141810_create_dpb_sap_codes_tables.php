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
        Schema::create('dpb_sap_operation_types', function (Blueprint $table) {
            $table->comment('SAP operation types (Druh výkonu)');
            $table->id();

            $table->string('title')->nullable(false)->comment('');

            $table->unique([
                'title',
            ], 'sap_operation_type_unique');
        });

        Schema::create('dpb_sap_expense_types', function (Blueprint $table) {
            $table->comment('SAP expense types (Nákladový druh)');
            $table->id();

            $table->string('title')->nullable(false)->comment('');

            $table->unique([
                'title',
            ], 'sap_expense_type_unique');
        });

        Schema::create('dpb_sap_operation_codes', function (Blueprint $table) {
            $table->comment('SAP operation codes');
            $table->id();

            $table->string('code')->comment('SAP code');
            $table->string('title')->comment('');

            $table->unique([
                'code',
            ], 'sap_operation_code_unique');
        });

        Schema::create('dpb_sap_operation_expense_sap_codes', function (Blueprint $table) {
            $table->comment('Pivot binding expense, operation, vehicle type and SAP codes');
            $table->id();

            $table->foreignId('operation_type_id')
                ->constrained('dpb_sap_operation_types', 'id')
                ->cascadeOnDelete();

            $table->foreignId('expense_type_id')
                ->constrained('dpb_sap_expense_types', 'id')
                ->cascadeOnDelete();

            $table->foreignId('sap_code_id')
                ->constrained('dpb_sap_operation_codes', 'id')
                ->cascadeOnDelete();

            $table->foreignId('vehicle_type_id')
                ->nullable(false)
                ->constrained('fleet_vehicle_types', 'id');

            // prevent duplicates (important for mapping tables)
            $table->unique([
                'operation_type_id',
                'expense_type_id',
                'sap_code_id',
            ], 'op_exp_sap_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dpb_sap_operation_expense_sap_codes');
        Schema::dropIfExists('dpb_sap_operation_codes');
        Schema::dropIfExists('dpb_sap_operation_types');
        Schema::dropIfExists('dpb_sap_expense_types');
    }
};
