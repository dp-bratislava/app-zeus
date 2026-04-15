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
        Schema::table('dpb_worktimefund_model_activityrecord', function (Blueprint $table) {
            $table->timestamps();
            $table->softDeletes();

            $table->index('updated_at', 'idx_updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dpb_worktimefund_model_activityrecord', function (Blueprint $table) {
            $table->dropIndex(['idx_updated_at']);
            $table->dropTimestamps();
            $table->dropSoftDeletes();
        });
    }
};
