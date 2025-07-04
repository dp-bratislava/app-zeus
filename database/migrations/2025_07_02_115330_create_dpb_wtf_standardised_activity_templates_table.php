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
        Schema::create('dpb_wtf_standardised_activity_templates', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('duration')
                ->nullable(false)
                ->comment('Expected duration in minutes');
            $table->boolean('is_divisible')
                ->default(false)
                ->comment('Divisible between multiple contracts');
            $table->integer('people')
                ->nullable(false)
                ->default(1)
                ->comment('Number of participants expected');
            // computed column with duration in minutes
            $table->integer('man_minutes')
                ->nullable()
                ->storedAs('duration * people')
                ->comment('Computed column with man_minutes');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dpb_wtf_standardised_activity_templates');
    }
};
