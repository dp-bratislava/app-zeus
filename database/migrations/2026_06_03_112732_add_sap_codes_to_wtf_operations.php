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
        Schema::table('dpb_worktimefund_model_operation', function (Blueprint $table) {
            $table->string('accident_sap_code')->nullable();
            $table->string('malfunction_sap_code')->nullable();
            $table->string('maintenance_sap_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dpb_worktimefund_model_operation', function (Blueprint $table) {
            $table->dropColumn([
                'accident_sap_code',
                'malfunction_sap_code',
                'maintenance_sap_code'
            ]);
        });
    }
};
