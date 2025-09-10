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
        Schema::create('dpb_att_attendance', function (Blueprint $table) {
            $table->comment('Daily attendance records');
            $table->id();
            $table->date('date');
            $table->unsignedBigInteger('calendar_group_id');
            $table->unsignedBigInteger('employee_contract_id');
            $table->time('shift_start');
            $table->integer('shift_duration')->nullable()->comment('Shift duration in minutes');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dpb_att_attendance');
    }
};
