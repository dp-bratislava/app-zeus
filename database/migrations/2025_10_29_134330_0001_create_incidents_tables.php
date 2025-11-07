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
        $tablePrefix = config('pkg-incidents.table_prefix');

        // incident  types
        Schema::create($tablePrefix . 'incident_types', function (Blueprint $table) use ($tablePrefix) {
            $table->id();

            $table->string('code')->nullable();
            $table->string('title')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // incidents
        Schema::create($tablePrefix . 'incidents', function (Blueprint $table) use ($tablePrefix) {
            $table->id();
            $table->date('date')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('type_id')
                ->nullable()
                ->comment('')
                ->constrained($tablePrefix . 'incident_types', 'id');
            $table->string('state')
                ->nullable()
                ->comment("Current incident state");                
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-incidents.table_prefix');

        Schema::dropIfExists($tablePrefix . 'incidents');
        Schema::dropIfExists($tablePrefix . 'incident_types');
    }
};
