<?php

namespace App\DataMigrations;

use App\DataMigrations\Contracts\DataMigration;
use Dpb\WorkTimeFund\Models\ActivityRecord;
use Dpb\WorkTimeFund\Models\BreakActivity;
use Dpb\WorkTimeFund\Models\Task;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Set all operations as scalable for department 9486
 */
class ActivityMigration9486 implements DataMigration
{
    private const DEPARTMENT_ID = 460;
    private const DEFAULT_STATUS = 'completed';
    private const NORMALISED_DATA_TABLE = 'import_format_example';
    private const PREPROCESSED_DATA_TABLE = 'tmp_kahatova_preprocessed_data';
    private const RAW_DATA_TABLE = '9486_combined';

    public function run(): void
    {
        // create import specific temp categories and operations
        // $this->createOperations();

        // // build operations map
        // $this->buildOperationsMapTable();

        // // build operations
        // $this->buildOperationsTable();

        // // fill import table
        // $this->fillImportTable();

        // create tasks and activity records
        $this->createActivityRecords();
    }

    private function createOperations(): void {}

    private function createActivityRecords(): void
    {
        DB::transaction(function () {
            // cleanup previous attempt
            Task::where('created_at', '>=', '2026-09-30 14:00:00')->forceDelete();
    
            $currentGroupId = null;
            $currentNormalisedRecords = collect();

            DB::table(self::NORMALISED_DATA_TABLE)
                ->whereNotNull('shareable_group')
                ->orderBy('shareable_group')
                // ->orderBy('id')
                // ->limit(100)
                ->lazy()
                ->each(function ($normalisedRecord) use (&$currentGroupId, &$currentNormalisedRecords) {

                    if (
                        $currentGroupId !== null &&
                        $normalisedRecord->shareable_group !== $currentGroupId
                    ) {
                        $this->processOperationGroup($currentNormalisedRecords);

                        $currentNormalisedRecords = collect();
                    }

                    $currentGroupId = $normalisedRecord->shareable_group;
                    $currentNormalisedRecords->push($normalisedRecord);
                });

            // Process final group
            if ($currentNormalisedRecords->isNotEmpty()) {
                $this->processOperationGroup($currentNormalisedRecords);
            }
        });
    }

    private function processOperationGroup(Collection $normalisedRecords): void
    {
        $first = $normalisedRecords->first();

        $task = Task::create([
            'source_id' => $first->operation_id,
            'title' => 'xxxx', //$first->operation,
            'expected_duration' => $first->real_duration,
            'department_id' => self::DEPARTMENT_ID,
            'status' => self::DEFAULT_STATUS,
            'maintainable_id' => $first->maintainable_id,
            'maintainable_type' => $first->maintainable_type,
        ]);

        // foreach ($normalisedRecords as $record) {
        //     ActivityRecord::create([
        //         'title' => $task->title,
        //         'type' => 'O',
        //         'expected_duration' => $task->expected_duration,
        //         'real_duration' => $task->expected_duration,
        //         'is_official' => 1,
        //         'is_fulfilled' => 1,
        //         'date' => $record->date,
        //         'personal_id' => $record->pid,
        //         'source_id' => $task->id, // worktime ??
        //         'parent_id' => $record->worktime_id, // worktime ??
        //         'task_id' => $task->id,
        //     ]);
        // }
    }

    private function fillImportTable(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        DB::statement('TRUNCATE TABLE ' . self::NORMALISED_DATA_TABLE);
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        $sql = "
            INSERT INTO " . self::NORMALISED_DATA_TABLE . " (
                operation_id,
                `date`,
                employee_contract_id,
                real_duration,	
                maintainable_id,
                maintainable_type,
                shareable_group
            )	
            SELECT
                kpd.operation_id,
                kpd.`date`,
                c.id,
                kpd.operation_duration,
                kpd.maintainable_id,
                kpd.maintainable_type,
                kpd.wtf_task_grouping_id
            FROM
                " . self::PREPROCESSED_DATA_TABLE . " kpd
                LEFT JOIN datahub_employee_contracts c ON c.pid = kpd.pid
            WHERE
                kpd.operation_id IS NOT NULL    
        ";

        DB::statement($sql);
    }

    private function buildOperationsMapTable(): void
    {
        Schema::dropIfExists('tmp_kahatova_operations_map');

        Schema::create('tmp_kahatova_operations_map', function (Blueprint $table) {
            $table->string('category', 255)->collation('utf8mb4_unicode_ci');
            $table->string('operation', 255)->nullable()->collation('utf8mb4_unicode_ci');
            $table->unsignedInteger('operation_duration')->nullable();
            $table->string('v_type', 10)->default('')->nullable()->collation('utf8mb4_unicode_ci');
            $table->integer('min_length')->nullable();
            $table->integer('max_length')->nullable();
            $table->string('typ_cistenia', 26)->nullable()->collation('utf8mb4_unicode_ci');
            $table->unsignedBigInteger('category_id')->default(0);
            $table->unsignedBigInteger('operation_id')->nullable()->default(0);
        });

        $sql = "
INSERT INTO tmp_kahatova_operations_map
SELECT 
 c.title AS category,
 o.title AS operation,
 o.duration AS operation_duration,
 case
 	when c.title LIKE '%A-%' then 'Autobus'
 	when c.title LIKE '%T-%' then 'Trolejbus'
 	when c.title LIKE 'Električky' then 'Električka'
 	
 	when pc.title LIKE '%A-%' AND c.title = 'Čistenie B' then 'Autobus'
 	when pc.title LIKE '%T-%' AND c.title = 'Čistenie B' then 'Trolejbus'
 	when pc.title LIKE 'Električky' AND c.title = 'Čistenie B' then 'Električka'
 	ELSE null
 END AS v_type,
 case
 	when pc.title = 'A-Bus do 12m' OR c.title = 'A-Bus do 12m' then 0
  	when pc.title = 'A-Bus nad 12m' OR c.title = 'A-Bus nad 12m' then 12
 	when pc.title = 'T-Bus 12m' OR c.title = 'T-Bus 12m' then 0
 	when pc.title = 'T-Bus 18m' OR c.title = 'T-Bus 18m' then 12
 	when pc.title = 'T-Bus 24m' OR c.title = 'T-Bus 24m' then 18
 	when pc.title = 'Električky' OR c.title = 'Električky' then 0 	
 	ELSE null
 END AS min_length, 
 case
 	when pc.title = 'A-Bus do 12m' OR c.title = 'A-Bus do 12m' then 12
  	when pc.title = 'A-Bus nad 12m' OR c.title = 'A-Bus nad 12m' then 100
 	when pc.title = 'T-Bus 12m' OR c.title = 'T-Bus 12m' then 12
 	when pc.title = 'T-Bus 18m' OR c.title = 'T-Bus 18m' then 18
 	when pc.title = 'T-Bus 24m' OR c.title = 'T-Bus 24m' then 100
 	when pc.title = 'Električky' OR c.title = 'Električky' then 100 	 	
 	ELSE null
 END AS max_length,  
 case
 	when o.title = 'Tepovanie sedadla vodiča' then 'Tepovanie - sedadlo vodiča'
	when o.title like 'Mimoriadne čistenie stropov pri výduchoch' then 'Strop a klimatizácia'
 	when o.title = 'Tepovanie sedadiel' then 'Tepovanie'
 	when o.title = 'Umývanie podláh na požiadanie z prevádzky' then 'Podlaha a schody' 	
	when o.title = 'Odstraňovanie biologického znečistenia (zvratky, exkrementy, potravinový odpad, apod.)' then 'Znečistenie' 	
	when o.title = 'Čistenie prilepených ťažko odstrániteľných nečistôť (žuvačky, nálepky, apod.)' then 'Mimoriadne práce' 	
-- 	when o.title like 'Čistenie grafitu%' then 'Grafity'
 	when o.title = '9486 - import - grafity' then 'Grafity'
	
 	when pc.title LIKE '%A-%' AND c.title = 'Čistenie B' then 'A komplexne'
 	when pc.title LIKE '%T-%' AND c.title = 'Čistenie B' then 'T komplexne'
 	when pc.title LIKE 'Električky' AND c.title = 'Čistenie B' then 'E komplexne'
 	ELSE null
 END AS typ_cistenia,  
 c.id AS category_id,
 o.id AS operation_id
FROM
	dpb_worktimefund_model_category c
	LEFT join dpb_worktimefund_model_category pc ON pc.id = c.parent_id
	LEFT JOIN dpb_worktimefund_model_operation o ON o.parent_id = c.id
	LEFT JOIN dpb_departments_mm_morphable_department md ON md.morphable_id = c.id AND md.morphable_type LIKE '%Category%'
WHERE
	md.department_id = " . self::DEPARTMENT_ID;

        DB::statement($sql);
    }

    private function buildOperationsTable(): void
    {
        Schema::dropIfExists(self::PREPROCESSED_DATA_TABLE);

        Schema::create(self::PREPROCESSED_DATA_TABLE, function (Blueprint $table) {
            $table->charset('utf8mb4');
            $table->collation('utf8mb4_unicode_ci');
            $table->string('Osobné číslo', 255)->nullable();
            $table->string('pid', 255)->nullable();
            $table->string('date', 10)->nullable();
            $table->string('vehicle_code', 255)->nullable();
            $table->string('model', 255)->nullable();
            $table->string('type', 255)->nullable();
            $table->string('typ_cistenia', 255)->nullable();
            $table->string('operation', 255)->nullable();
            $table->decimal('length', 8, 2)->nullable()->comment('Length in meters');
            $table->integer('min_length')->nullable();
            $table->integer('max_length')->nullable();
            $table->unsignedInteger('operation_duration')->nullable();
            $table->integer('people_total')->nullable();
            $table->unsignedBigInteger('operation_id')->nullable()->default(0);
            $table->unsignedBigInteger('maintainable_id')->nullable()->default(0);
            $table->string('maintainable_type', 45)->default('');
            $table->string('wtf_task_grouping_id', 32)->nullable();
        });

        $baseSql = "
            INSERT INTO `" . self::PREPROCESSED_DATA_TABLE . "`
            SELECT distinct
                ku.`Osobné číslo`,
                SUBSTRING_INDEX(ku.`Osobné číslo`, ' - ', 1) AS pid,
                DATE_FORMAT(
                STR_TO_DATE(ku.`Dátum`, '%e.%c.%Y'),
                '%Y-%m-%d'
                ) AS `date`,
                vss.`code` AS vehicle_code,
                vm.title AS model,
                vss.`type`,
                ku.`Typ čistenia` AS typ_cistenia,
                o.operation as operation,
                vm.`length`,
                o.min_length,
                o.max_length,
                o.operation_duration,
                ku.`Počet pracovníkov` AS people_total,	
                o.operation_id AS operation_id,
                v.id AS maintainable_id,
                'Dpb\\\\WorkTimeFund\\\\Models\\\\Maintainables\\\\Vehicle' AS maintainable_type,
            MD5(CONCAT_WS('|',
                COALESCE(
                    DATE_FORMAT(
                    STR_TO_DATE(ku.`Dátum`, '%e.%c.%Y'),
                    '%Y-%m-%d'
                    ), ''),
                COALESCE(vss.`code`, ''),
                COALESCE(o.operation_id, '')
            )) AS wtf_task_grouping_id	
            FROM
            mvw_fleet_vehicle_snapshots vss
            LEFT JOIN fleet_vehicles v ON v.id = vss.vehicle_id
            left JOIN fleet_vehicle_models vm ON vm.id = v.model_id
            left JOIN " . self::RAW_DATA_TABLE . " ku ON ku.`Čislo vozidla` = vss.`code`
            left JOIN tmp_kahatova_operations_map o ON         
        ";

        // Normal operations 
        $normalCond = "
            o.v_type = vss.`type` 
            AND o.typ_cistenia = ku.`Typ čistenia`
            AND FLOOR(vm.`length`) > o.min_length 
            AND FLOOR(vm.`length`) <= o.max_length    
        WHERE
            ku.`Typ čistenia` not IN ('Grafity')
        ";
        $normalQuery = $baseSql . " " . $normalCond;
        DB::statement($normalQuery);

        // Grafity operations 
        $grafityCond = "
            o.typ_cistenia = ku.`Typ čistenia`
            and ku.`Typ čistenia` = 'Grafity'
        ";
        $grafitylQuery = $baseSql . " " . $grafityCond;
        DB::statement($grafitylQuery);

        Schema::table(self::PREPROCESSED_DATA_TABLE, function (Blueprint $table) {
            $table->index(['wtf_task_grouping_id'], 'idx_kahatova_group');
        });
    }
}
