<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('datahub_attendance_groups', function (Blueprint $table) {
            $table->comment('List of attendance groups from Wega system');
            $table->id();
            $table->string('code', 2)->nullable(false)->unique()->comment('Unique code of attendance group from Wega system');
            $table->string('title')->nullable()->comment('Title of attendance group from Wega system');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('datahub_attendance_groups');
    }
};
