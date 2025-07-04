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
        Schema::create('dpb_wtf_planned_activities', function (Blueprint $table) {
            $table->comment('List of planned activities');
            $table->id();

            $table->date('date')->nullable();
            $table->unsignedBigInteger('template_id');
            $table->integer('duration')
                ->nullable()
                ->comment('Planned duration in minutes');
            $table->unsignedBigInteger('employee_contract_id');
            $table->unsignedBigInteger('status_id');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dpb_wtf_planned_activities');
    }
};
