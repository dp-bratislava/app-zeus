<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('datahub_attendance_archives', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('datahub_department_id')->constrained('datahub_departments');
            $table->foreignId('datahub_contract_id')->constrained('datahub_employee_contracts');
            $table->foreignId('datahub_attendance_shift_id')->constrained('datahub_attendance_shifts');
            $table->timestamps();
        });
    }
};
