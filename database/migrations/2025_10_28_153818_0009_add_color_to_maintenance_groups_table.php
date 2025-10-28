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
        $tablePrefix = config('pkg-fleet.table_prefix');

        // maintenance group colors
        Schema::table($tablePrefix . 'maintenance_groups', function (Blueprint $table) {
            $table->string('color')->nullable()->after('description');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-fleet.table_prefix');

        Schema::table($tablePrefix . 'maintenance_groups', function (Blueprint $table) use ($tablePrefix) {
            $table->dropColumn('color');
        });
    }
};
