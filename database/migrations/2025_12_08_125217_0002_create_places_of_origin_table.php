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
        $tablePrefix = config('pkg-tasks.table_prefix');

        // task place_of_origins
        Schema::create($tablePrefix . 'places_of_origin', function (Blueprint $table) {
            $table->id();
            $table->string('uri')
                ->nullable(false)
                ->unique()
                ->comment('Unique code to identify place_of_origin in application layer');
            $table->string('title')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // add place_of_origin to task
        Schema::table($tablePrefix . 'tasks', function (Blueprint $table) use ($tablePrefix) {
            $table->foreignId('place_of_origin_id')
                ->nullable()
                ->comment('place_of_origin of the task')
                ->after('state')
                ->constrained($tablePrefix . 'places_of_origin', 'id');
        });            
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-tasks.table_prefix');

        Schema::table($tablePrefix . 'tasks', function (Blueprint $table) use ($tablePrefix) {
            $table->dropConstrainedForeignId('place_of_origin_id');
        });

        Schema::dropIfExists($tablePrefix . 'places_of_origin');
    }
};
