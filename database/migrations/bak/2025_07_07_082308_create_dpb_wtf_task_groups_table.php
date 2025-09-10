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
        // Schema::create('dpb_wtf_task_groups', function (Blueprint $table) {
        //     $table->comment('Groups of task to distinguish list of task subjects by');
        //     $table->id();
        //     $table->string('code')->nullable(false)->unique();
        //     $table->string('title')->nullable(false);
        //     $table->string('description')->nullable();
        //     $table->timestamps();
        //     $table->softDeletes();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('dpb_wtf_task_groups');
    }
};
