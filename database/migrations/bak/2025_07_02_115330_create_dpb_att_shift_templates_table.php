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
        Schema::create('dpb_att_shift_templates', function (Blueprint $table) {
            $table->comment('Predefined shift templates');
            $table->id();
            $table->string('code')->nullable();
            $table->string('title');
            $table->time('time_from')->nullable();
            $table->time('time_to')->nullable();
            $table->integer('duration')->nullable()->comment('Duration in minutes');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dpb_att_shift_templates');
    }
};
