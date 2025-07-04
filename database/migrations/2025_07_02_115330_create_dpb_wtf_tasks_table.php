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
        Schema::create('dpb_wtf_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable()->comment('Parent task to handle task hierarchy.');
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->dateTime('date')->nullable();
            $table->dateTime('deadline')->nullable();
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('priority_id')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();;
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dpb_wtf_tasks');
    }
};
