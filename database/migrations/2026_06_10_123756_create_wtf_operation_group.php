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
        // add operations groups table
        Schema::create('dpb_worktimefund_model_operation_groups', function (Blueprint $table) {
            $table->comment('Groups of operations');
            $table->id();
            $table->string('title')->nullable(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('dpb_worktimefund_model_operation', function (Blueprint $table) {
            $table->foreignId('group_id')
                ->after('parent_id')
                ->nullable()
                ->comment('Group of operations e.g. engine, tire, ...')
                ->constrained('dpb_worktimefund_model_operation_groups', 'id', 'fk_operation_group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dpb_worktimefund_model_operation', function (Blueprint $table) {
            $table->dropForeign('fk_operation_group');
            $table->dropColumn('group_id');
        });

        Schema::dropIfExists('dpb_worktimefund_model_operation_groups');
    }
};
