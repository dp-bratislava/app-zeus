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
        Schema::create('dpb_wtf_task_subjects', function (Blueprint $table) {
            $table->comment('List of subjects bound to task. E.g. vehicle, building, notebook, ...');
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('subject_id');
            $table->string('subject_type');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dpb_wtf_task_subjects');
    }
};
