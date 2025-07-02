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
        Schema::table('dpb_wtf_activities', function (Blueprint $table) {
            $table->foreign(['group_id'])->references(['id'])->on('dpb_wtf_activity_groups')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['type_id'])->references(['id'])->on('dpb_wtf_activity_types')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dpb_wtf_activities', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropForeign(['type_id']);
        });
    }
};
