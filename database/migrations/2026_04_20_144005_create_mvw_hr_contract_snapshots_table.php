<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mvw_hr_contract_snapshots', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('contract_id');
            $table->string('pid', 10);
            $table->string('department_code', 50);
            $table->string('department_title', 255);
            $table->string('profession_code', 50);
            $table->string('profession_title', 255);
            $table->string('first_name', 255);
            $table->string('last_name', 255);
            $table->string('gender', 10);
            $table->string('contract_type_uri', 255);
            $table->string('contract_type_title', 255);
            $table->string('employee_circuit_code', 50);
            $table->boolean('is_active');
            $table->boolean('is_primary');
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            $table->string('hash', 255)->comment('Unique employee hash');

            $table->timestamps();

            $table->unique(['contract_id'], 'uniq_hr_contract_snapshot');
            $table->index('contract_id');
            $table->index('updated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mvw_hr_contract_snapshots');
    }
};