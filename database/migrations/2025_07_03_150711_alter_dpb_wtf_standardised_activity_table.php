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
        Schema::table('dpb_wtf_standardised_activities', function (Blueprint $table) {
            $table->foreignId('task_id')
                ->constrained('dpb_wtf_tasks', 'id')
                ->onUpdate('no action')
                ->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dpb_wtf_standardised_activities', function (Blueprint $table) {
            $table->dropConstrainedForeignId('task_id');
        });

    }
};
