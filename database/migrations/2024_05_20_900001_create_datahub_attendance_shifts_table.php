<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('datahub_attendance_shifts', function (Blueprint $table) {
            $table->comment('List of shift types from Wega system');
            $table->id();
            $table->string('code', 2)->nullable(false)->comment('Unique code of shift type from Wega system');
            $table->string('title')->nullable()->comment('Title of shift type from Wega system');
            $table->time('time_from')->nullable(false)->comment('Shift starting time');
            $table->time('time_to')->nullable(false)->comment('Shift ending time');
            $table->integer('duration')->nullable(false)->comment('Shift duration time in minutes');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('datahub_attendance_shifts');
    }
};
