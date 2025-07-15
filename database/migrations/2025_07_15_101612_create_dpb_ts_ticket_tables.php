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
        // ticket statuses
        Schema::create('dpb_ts_ticket_statuses', function (Blueprint $table) {
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
        Schema::create('dpb_ts_ticket_priorities', function (Blueprint $table) {
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
        Schema::create('dpb_ts_ticket_groups', function (Blueprint $table) {
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
        Schema::create('dpb_ts_tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable()->comment('Parent ticket to handle ticket hierarchy.');
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->dateTime('date')->nullable();
            $table->dateTime('deadline')->nullable();
            $table->foreignId('department_id')
                ->nullable(false)
                ->constrained('datahub_departments');
            $table->foreignId('priority_id')->nullable()->constrained('dpb_ts_ticket_priorities', 'id');
            $table->foreignId('status_id')->nullable()->constrained('dpb_ts_ticket_statuses', 'id');
            $table->foreignId('group_id')->nullable()->constrained('dpb_ts_ticket_groups', 'id');
            $table->timestamps();
            $table->softDeletes();
        });

        // ticket materials
        Schema::create('dpb_ts_ticket_materials', function (Blueprint $table) {
            $table->comment('List of materials used for this ticket');
            $table->id();
            $table->foreignId('ticket_id')
                ->nullable(false)
                ->constrained('dpb_ts_tickets', 'id');
            $table->string('title');
            $table->decimal('unit_price');
            $table->decimal('vat');
            $table->integer('quantity');
            $table->timestamps();
            $table->softDeletes();
        });

        // ticket subjects
        Schema::create('dpb_ts_ticket_subjects', function (Blueprint $table) {
            $table->comment('List of subjects bound to ticket. E.g. vehicle, building, notebook, ...');
            $table->id();
            $table->foreignId('ticket_id')
                ->nullable(false)
                ->constrained('dpb_ts_tickets', 'id');
            $table->unsignedBigInteger('subject_id')->index();
            $table->string('subject_type')->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dpb_ts_ticket_subjects');
        Schema::dropIfExists('dpb_ts_ticket_materials');
        Schema::dropIfExists('dpb_ts_tickets');
        Schema::dropIfExists('dpb_ts_ticket_statuses');
        Schema::dropIfExists('dpb_ts_ticket_priorities');
        Schema::dropIfExists('dpb_ts_ticket_groups');
    }
};
