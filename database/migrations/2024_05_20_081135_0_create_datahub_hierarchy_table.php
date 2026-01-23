<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('datahub_hierarchy', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('description');
            $table->foreignId('datahub_hierarchy_id')->nullable()->constrained('datahub_hierarchy')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('datahub_hierarchy');
    }
};
