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
        $tablePrefix = config('pkg-tickets.table_prefix');

        // ticket statuses
        Schema::create($tablePrefix . 'ticket_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('code')
                ->nullable(false)
                ->unique()
                ->comment('Unique code to identify status in application layer');
            $table->string('title')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // ticket priorities
        Schema::create($tablePrefix . 'ticket_priorities', function (Blueprint $table) {
            $table->id();
            $table->string('code')
                ->nullable(false)
                ->unique()
                ->comment('Unique code to identify priority in application layer');
            $table->string('title')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // ticket groups
        Schema::create($tablePrefix . 'ticket_groups', function (Blueprint $table) {
            $table->comment('List of ticket groups e.g. IT, sprava budov, vozy, ...');
            $table->id();
            $table->string('code')
                ->nullable(false)
                ->unique()
                ->comment('Unique code to identify group in application layer');
            $table->string('title')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // tickets
        Schema::create($tablePrefix . 'tickets', function (Blueprint $table) use ($tablePrefix) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable()->comment('Parent ticket to handle ticket hierarchy.');
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->dateTime('date')->nullable();
            $table->dateTime('deadline')->nullable();
            $table->foreignId('priority_id')->nullable()->constrained($tablePrefix . 'ticket_priorities', 'id');
            $table->foreignId('status_id')->nullable()->constrained($tablePrefix . 'ticket_statuses', 'id');
            $table->foreignId('group_id')->nullable()->constrained($tablePrefix . 'ticket_groups', 'id');
            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-tickets.table_prefix');

        Schema::dropIfExists($tablePrefix . 'tickets');
        Schema::dropIfExists($tablePrefix . 'ticket_statuses');
        Schema::dropIfExists($tablePrefix . 'ticket_priorities');
        Schema::dropIfExists($tablePrefix . 'ticket_groups');
    }
};
