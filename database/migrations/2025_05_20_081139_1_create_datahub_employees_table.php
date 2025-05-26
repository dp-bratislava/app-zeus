<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('datahub_employees', function (Blueprint $table) {
            $table->id();
            $table->string('hash')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('prefix_titles')->nullable();
            $table->string('suffix_titles')->nullable();
            $table->string('gender')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('datahub_employees');
    }
};
