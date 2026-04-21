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

            $table->string('subject_type', 50);
            $table->unsignedBigInteger('subject_id');

            $table->string('label', 255);
            $table->string('code', 100)->nullable();

            $table->json('meta')->nullable();

            $table->timestamps();

            $table->unique(['subject_type', 'subject_id'], 'uniq_subject_snapshot');
            $table->index('subject_type');
            $table->index('subject_id');
            $table->index('updated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mvw_work_task_subject_snapshots');
    }
};