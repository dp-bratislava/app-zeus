<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('datahub_employee_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('pid');
            $table->foreignId('datahub_employee_id')->nullable()->constrained('datahub_employees')->nullOnDelete();
            $table->foreignId('datahub_department_id')->nullable()->constrained('datahub_departments')->nullOnDelete();
            $table->foreignId('datahub_profession_id')->nullable()->constrained('datahub_professions')->nullOnDelete();
            $table->foreignId('circuit_id')->nullable()->constrained('datahub_employee_circuits')->nullOnDelete();
            $table->foreignId('type_id')->nullable();
            $table->string('valid_from')->nullable();
            $table->boolean('is_active');
            $table->boolean('is_primary');
            $table->foreignId('datahub_contract_type_id')->nullable()->constrained('datahub_contract_types')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('datahub_employee_contracts');
    }
};
