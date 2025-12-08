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

        // task items
        Schema::create($tablePrefix . 'task_items', function (Blueprint $table) use ($tablePrefix) {
            $table->id();
            $table->dateTime('date')->nullable();
            $table->foreignId('task_id')
                ->nullable()
                ->comment('Parent task')
                ->constrained($tablePrefix . 'tasks', 'id');
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->string('state')
                ->nullable()
                ->comment("Current task item state");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-tasks.table_prefix');

        Schema::dropIfExists($tablePrefix . 'task_items');
    }
};
