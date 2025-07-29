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
        // // inspection template types
        // Schema::create('dpb_fleet_inspection_template_types', function (Blueprint $table) {
        //     $table->comment('List of tempalte types');
        //     $table->id();
        //     $table->string('code')
        //         ->nullable(false)
        //         ->unique()
        //         ->comment('Unique code to identify status in application layer');            
        //     $table->string('title');
        //     $table->string('description')->nullable();
        //     $table->timestamps();
        //     $table->softDeletes();
        // });

        // inspection statuses
        Schema::create('dpb_fleet_inspection_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('code')
                ->nullable(false)
                ->unique()
                ->comment('Unique code to identify status in application layer');
            $table->string('title')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // inspection templates
        Schema::create('dpb_fleet_inspection_templates', function (Blueprint $table) {
            $table->comment('List of vehicle inspection templates');
            $table->id();
            $table->string('title');
            $table->boolean('is_one_time')
                ->nullable(false)
                ->default(false)
                ->comment('One time inspection');
            $table->boolean('is_periodical')
                ->nullable(false)
                ->default(false)
                ->comment('Is periodical');
            $table->text('note')->nullable(true);
            $table->integer('distance_interval')
                ->nullable(true)
                ->comment('When should the inspection occur. After distance traveled in km');
            $table->integer('distance_first_advance')
                ->nullable(true)
                ->comment('TO DO in km');
            $table->integer('distance_second_advance')
                ->nullable(true)
                ->comment('TO DO in km');
            $table->integer('time_interval')
                ->nullable(true)
                ->comment('When should the inspection occur. After in service for specified months');
            $table->integer('time_first_advance')
                ->nullable(true)
                ->comment('TO DO in days');
            $table->integer('time_second_advance')
                ->nullable(true)
                ->comment('TO DO in days');

            // TO DO          
            // $table->foreignId('parent_id')
            //     ->nullable(true)
                //->comment('Prere') 
            //     ->constrained('dpb_fleet_inspection_templates', 'id');            
            // $table->foreignId('task_group_id')
            //     ->nullable(false)
            //     ->constrained('dpb_ts_task_groups', 'id');


            $table->timestamps();
            $table->softDeletes();
        });

        // // pivot vehicle model inspection
        // Schema::create('dpb_fleet_vehicle_model_inspeciton_template', function (Blueprint $table) {
        //     $table->comment('Pivot for vehicle model and inspection tmplate');
        //     $table->id();
        //     $table->foreignId('task_id')
        //         ->nullable(false)
        //         ->constrained('dpb_ts_tasks', 'id');
        //     $table->foreignId('employee_contract_id')
        //         ->nullable(false)
        //         ->constrained('datahub_employee_contracts', 'id');
        //     $table->date('date')->nullable();
        //     $table->dateTime('time_from')->nullable();
        //     $table->dateTime('time_to')->nullable();
        //     $table->text('note')->nullable();
        //     // computed column with duration in minutes
        //     $table->integer('duration')
        //         ->nullable()
        //         ->storedAs('TIMESTAMPDIFF(MINUTE, time_from, time_to)')
        //         ->comment('Computed column with real duration in minutes');
        //     $table->timestamps();
        //     $table->softDeletes();
        // });

        // inspection prerequisites
        Schema::create('dpb_fleet_inspection_prerequisite', function (Blueprint $table) {
            $table->comment('List of vehicle inspection prerequsites');
            $table->id();
            $table->foreignId('inspection_template_id')
                ->nullable()
                ->constrained(
                    'dpb_fleet_inspection_templates',
                    'id',
                    'idx_dpb_fleet_inspection_template_foreign'
                );
            $table->foreignId('prerequisite_id')
                ->nullable()
                ->comment('Another inspection template that is prerequisite for the specific template.')
                ->constrained(
                    'dpb_fleet_inspection_templates',
                    'id',
                    'idx_dpb_fleet_inspection_prerequisite_foreign'
                );
            $table->timestamps();
            $table->softDeletes();
        });

        // inspections
        Schema::create('dpb_fleet_inspections', function (Blueprint $table) {
            $table->comment('List of vehicle inspectons');
            $table->id();
            $table->date('date_planned_for')->nullable();
            $table->date('date_start')->nullable();
            $table->date('date_end')->nullable();
            $table->foreignId('vehicle_id')
                ->nullable(false)
                ->constrained('dpb_fleet_vehicles', 'id');
            $table->foreignId('inspection_template_id')
                ->nullable()
                ->constrained('dpb_fleet_inspection_templates', 'id');
            $table->foreignId('service_group_id')
                ->nullable()
                ->constrained('dpb_fleet_service_groups', 'id');                
            $table->foreignId('status_id')
                ->nullable(false)
                ->constrained('dpb_fleet_inspection_statuses', 'id');
            $table->integer('distance_traveled')
                ->nullable(true)
                ->comment('Distance traveled in km');
            $table->text('note')->nullable(true);
            $table->text('failures')
                ->nullable(true)
                ->comment('Description of failures found during inspection');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dpb_fleet_inspections');
        Schema::dropIfExists('dpb_fleet_inspection_prerequisites');
        Schema::dropIfExists('dpb_fleet_inspection_templates');
        Schema::dropIfExists('dpb_fleet_inspection_statuses');
        // Schema::dropIfExists('dpb_fleet_inspection_template_types');
    }
};
