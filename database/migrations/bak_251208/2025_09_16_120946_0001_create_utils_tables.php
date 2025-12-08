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
        $tablePrefix = config('pkg-utils.table_prefix');

        // measurement units
        Schema::create($tablePrefix . 'measurement_units', function (Blueprint $table) {
            $table->id();

            $table->string('code')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        // currency
        Schema::create($tablePrefix . 'currency', function (Blueprint $table) {
            $table->id();

            $table->string('code')->nullable();
            $table->string('title')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-utils.table_prefix');

        Schema::dropIfExists($tablePrefix . 'measurement_units');
        Schema::dropIfExists($tablePrefix . 'currency');
    }
};
