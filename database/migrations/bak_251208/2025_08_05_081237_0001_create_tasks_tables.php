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

        // task statuses
        Schema::create($tablePrefix . 'task_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('code')
                ->nullable(false)
                ->unique()
                ->comment('Unique code to identify status in application layer');
            $table->string('title')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // tasks
        Schema::create($tablePrefix . 'tasks', function (Blueprint $table) use ($tablePrefix) {
            $table->comment('List of Tasks');
            $table->id();
            $table->date('date')->nullable();

            $table->foreignId('status_id')->nullable()->constrained($tablePrefix . 'task_statuses', 'id');
            $table->foreignId('parent_id')->nullable()->constrained($tablePrefix . 'tasks', 'id');
            $table->text('description')->nullable();
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

        Schema::dropIfExists($tablePrefix . 'tasks');
        Schema::dropIfExists($tablePrefix . 'task_statuses');
    }
};
