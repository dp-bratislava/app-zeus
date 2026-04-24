<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mvw_work_task_subject_snapshots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id')
                ->comment('Work time fund task the subject belongs to');

            $table->string('subject_type', 255);
            $table->string('subject_label', 255);

            $table->timestamps();

            $table->unique(['subject_type', 'task_id'], 'uniq_subject_snapshot');
            $table->index('task_id');
            $table->index('subject_type');
            $table->index('subject_label');
            $table->index('updated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mvw_work_task_subject_snapshots');
    }
};