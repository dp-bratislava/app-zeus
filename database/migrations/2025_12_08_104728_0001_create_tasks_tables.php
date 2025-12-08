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
        // Schema::create($tablePrefix . 'task_statuses', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('code')
        //         ->nullable(false)
        //         ->unique()
        //         ->comment('Unique code to identify status in application layer');
        //     $table->string('title')->nullable();
        //     $table->timestamps();
        //     $table->softDeletes();
        // });

        // task priorities
        Schema::create($tablePrefix . 'task_priorities', function (Blueprint $table) {
            $table->id();
            $table->string('code')
                ->nullable(false)
                ->unique()
                ->comment('Unique code to identify priority in application layer');
            $table->string('title')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // task groups
        Schema::create($tablePrefix . 'task_groups', function (Blueprint $table) use ($tablePrefix) {
            $table->comment('List of task groups e.g. IT, sprava budov, vozy, ...');
            $table->id();
            $table->string('code')
                ->nullable(false)
                ->unique()
                ->comment('Unique code to identify group in application layer');
            $table->string('title')->nullable();
            $table->foreignId('parent_id')
                ->nullable()
                ->comment('Parent group for hierarchical strucuring of groups')
                ->constrained($tablePrefix . 'task_groups', 'id');
            $table->timestamps();
            $table->softDeletes();
        });

        // tasks
        Schema::create($tablePrefix . 'tasks', function (Blueprint $table) use ($tablePrefix) {
            $table->id();
            $table->foreignId('parent_id')
                ->nullable()
                ->comment('Parent task to handle task hierarchy.')
                ->constrained($tablePrefix . 'tasks', 'id');
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->dateTime('date')->nullable();
            $table->dateTime('deadline')->nullable();
            $table->foreignId('priority_id')->nullable()->constrained($tablePrefix . 'task_priorities', 'id');
            $table->foreignId('group_id')->nullable()->constrained($tablePrefix . 'task_groups', 'id');
            $table->string('state')
                ->nullable()
                ->comment("Current task state");
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
        Schema::dropIfExists($tablePrefix . 'task_priorities');
        Schema::dropIfExists($tablePrefix . 'task_groups');
    }
};
