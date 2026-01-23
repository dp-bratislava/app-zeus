<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('datahub_attendance_attendance', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT

            $table->date('date');

            $table->unsignedBigInteger('datahub_contract_id');
            $table->unsignedBigInteger('datahub_attendance_shift_id');

            // TIMESTAMP NULL DEFAULT NULL
            $table->timestamps();

            // UNIQUE INDEX (date, datahub_contract_id)
            $table->unique(
                ['date', 'datahub_contract_id'],
                'datahub_attendance_archive_date_datahub_contract_id_unique'
            );

            // INDEXES
            $table->index(
                'datahub_contract_id',
                'datahub_attendance_archive_datahub_contract_id_foreign'
            );

            $table->index(
                'datahub_attendance_shift_id',
                'datahub_attendance_archive_datahub_attendance_shift_id_foreign'
            );

            // FOREIGN KEYS
            $table->foreign('datahub_contract_id', 'datahub_attendance_archive_datahub_contract_id_foreign')
                ->references('id')
                ->on('datahub_employee_contracts')
                ->noActionOnUpdate()
                ->noActionOnDelete();

            $table->foreign(
                'datahub_attendance_shift_id',
                'datahub_attendance_archive_datahub_attendance_shift_id_foreign'
            )
                ->references('id')
                ->on('datahub_attendance_shifts')
                ->noActionOnUpdate()
                ->noActionOnDelete();
        });

        // Optional: set table collation explicitly
        Schema::table('datahub_attendance_attendance', function (Blueprint $table) {
            $table->collation = 'utf8mb4_unicode_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('datahub_attendance_attendance');
    }
};
