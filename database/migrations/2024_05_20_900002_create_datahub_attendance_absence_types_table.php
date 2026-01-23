<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('datahub_attendance_absence_types', function (Blueprint $table) {
            $table->comment('List of absence types from Wega system');
            $table->id();
            $table->string('code')->nullable(false)->unique()->comment('Unique code of absence type from Wega system');
            $table->string('title')->nullable(false)->comment('Title of absence type from Wega system');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('datahub_attendance_absence_types');
    }
};
