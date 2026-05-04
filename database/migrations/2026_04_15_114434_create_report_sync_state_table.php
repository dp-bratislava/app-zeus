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
        Schema::create('report_sync_state', function (Blueprint $table) {
            $table->id();

            $table->string('report_name')->nullable(false)->unique();
            $table->dateTime('last_synced_at')->nullable();
            $table->bigInteger('last_synced_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_sync_state');
    }
};
