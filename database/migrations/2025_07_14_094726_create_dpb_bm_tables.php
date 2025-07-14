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
        // compounds
        Schema::create('dpb_bm_compounds', function (Blueprint $table) {
            $table->comment('List of compounds');
            $table->id();
            $table->string('code')->nullable();
            $table->string('title');
            $table->string('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // buildings
        Schema::create('dpb_bm_buildings', function (Blueprint $table) {
            $table->comment('List of buildings');
            $table->id();
            $table->string('code')->nullable();
            $table->string('title');
            $table->string('description')->nullable();
            $table->foreignId('compound_id')
                ->nullable()
                ->constrained('dpb_bm_compounds', 'id');
            $table->timestamps();
            $table->softDeletes();
        });

        // offices
        Schema::create('dpb_bm_offices', function (Blueprint $table) {
            $table->comment('List of offices');
            $table->id();
            $table->string('code')->nullable();
            $table->string('description')->nullable();
            $table->foreignId('building_id')
                ->nullable()
                ->constrained('dpb_bm_buildings', 'id');            
            $table->integer('floor')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dpb_bm_offices');
        Schema::dropIfExists('dpb_bm_buildings');
        Schema::dropIfExists('dpb_bm_compounds');
    }
};
