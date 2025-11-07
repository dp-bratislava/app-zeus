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
        Schema::create('dpb_wtf_task_materials', function (Blueprint $table) {
            $table->comment('List of materials used for this task');
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->string('title');
            $table->decimal('unit_price');
            $table->decimal('vat');
            $table->integer('quantity');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dpb_wtf_task_materials');
    }
};
