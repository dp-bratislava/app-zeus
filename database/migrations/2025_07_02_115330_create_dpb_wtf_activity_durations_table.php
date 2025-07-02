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
        Schema::create('dpb_wtf_activity_durations', function (Blueprint $table) {
            $table->comment('Predefined expected duration options for activities');
            $table->id();
            $table->unsignedBigInteger('activity_id');
            $table->integer('duration')->nullable()->comment('Expected duration in minutes');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dpb_wtf_activity_durations');
    }
};
