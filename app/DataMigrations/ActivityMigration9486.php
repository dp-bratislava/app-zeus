<?php

namespace App\DataMigrations;

use App\DataMigrations\Contracts\DataMigration;
use Dpb\Package\Fleet\Models\VehicleModel;
use Dpb\WorkTimeFund\Models\Category;
use Dpb\WorkTimeFund\Models\Operation;
use Dpb\WorkTimeFund\Models\Task;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
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
    private const MAPPED_DATA_TABLE = 'tmp_kahatova_mapped_data';
    private const UNMAPPED_DATA_TABLE = 'tmp_kahatova_unmapped_data';
    private const RAW_DATA_TABLE = '9486_combined';

    public function run(): void
    {
        // fix existing data
        $this->fixExistingData();

        // create import specific temp categories and operations
        $this->createOperations();

        // // build operations map
        $this->buildOperationsMapTable();

        // // build operations
        $this->fillPreprocessedDataTable();

        // // fill import table
        $this->fillImportTable();

        // create tasks and activity records
        // $this->createActivityRecords();
    }

    private function createOperations(): void
    {
        $records = [
            '9486 - import - grafity' => 900,
            '9486 - import - nenamapované' => 60,
        ];

        foreach ($records as $title => $duration) {
            $category = Category::where('title', $title)->first();

            if ($category == null) {
                $category = Category::create([
                    'title' => $title,
                    'type' => 'vehicles',
                    'parent_id' => NULL,
                    'sorting' => '',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'is_official' => 1,
                ]);
            }

            $operation = Operation::where('title', $title)->first();
            if ($operation == null) {
                Operation::create([
                    'title' => $title,
                    'description' => '',
                    'duration' => $duration,
                    'parent_id' => $category->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'is_official' => 1,
                    'is_shareable' => 0,
                    'is_scalable' => 1
                ]);
            }

            $exists = DB::table('dpb_departments_mm_morphable_department')
                ->where('department_id', self::DEPARTMENT_ID)
                ->where('morphable_id', $category->id)
                ->where('morphable_type', 'Dpb\\WorkTimeFund\\Models\\Category')
                ->exists();

            if (!$exists) {
                DB::table('dpb_departments_mm_morphable_department')
                    ->insert([
                        'department_id' => self::DEPARTMENT_ID,
                        'morphable_id' => $category->id,
                        'morphable_type' => 'Dpb\\WorkTimeFund\\Models\\Category',
                    ]);
            }
        }
    }

    private function fixExistingData(): void
    {
        $lengths = [
            'Ikarus 280' => 18,
            'K2' => 20.4,
            'Karosa B 732 CNG' => 16.53,
            'Karosa B 741 CNG' => 17.35,
            'Otokar E-kent' => 12,
            'Škoda 21 Tr' => 11.76,
            'Škoda Sanos S 200' => 17.72,
            'SOR C 10,5' => 10.78,
            'SOR NSG 18' => 18.75,
            'T2' => 20.4,
            'TAM 272' => 18,
            'Škoda 15 Tr 13/6 M' => 17.72,
            'FBW' => 12.12,
            'Škoda ŠM 11' => 18,
            'DPMB' => 20.4, // historicka elektricka
        ];

        foreach ($lengths as $model => $length) {
            VehicleModel::where('title', $model)->update(['length' => $length]);
            DB::table(self::RAW_DATA_TABLE)
                ->where('vehicle_model_title', $model)
                ->update(['vehicle_length' => $length]);
        }
        // VehicleModel::where('title', 'K2')->update(['length' => 20.4]);
        // VehicleModel::where('title', 'Karosa B 732 CNG')->update(['length' => 16.53]);
        // VehicleModel::where('title', 'Karosa B 741 CNG')->update(['length' => 17.35]);
        // VehicleModel::where('title', 'Otokar E-kent')->update(['length' => 12]);
        // VehicleModel::where('title', 'Škoda 21 Tr')->update(['length' => 11.76]);
        // VehicleModel::where('title', 'Škoda Sanos S 200')->update(['length' => 17.72]);
        // VehicleModel::where('title', 'SOR C 10,5')->update(['length' => 10.78]);
        // VehicleModel::where('title', 'SOR NSG 18')->update(['length' => 18.75]);
        // VehicleModel::where('title', 'T2')->update(['length' => 20.4]);
        // VehicleModel::where('title', 'TAM 272')->update(['length' => 18]);

        // DB::table(self::RAW_DATA_TABLE)->where('vehicle_model_title', 'Ikarus 280')->update(['vehicle_length' => 12]);
        // DB::table(self::RAW_DATA_TABLE)->where('vehicle_model_title', 'Otokar E-kent')->update(['vehicle_length' => 12]);
        // DB::table(self::RAW_DATA_TABLE)->where('vehicle_model_title', 'SOR C 10,5')->update(['vehicle_length' => 10.78]);
        // DB::table(self::RAW_DATA_TABLE)->where('vehicle_model_title', 'SOR NSG 18')->update(['vehicle_length' => 18.75]);
    }

    private function fillImportTable(): void
    {
        Schema::dropIfExists(self::NORMALISED_DATA_TABLE);

        Schema::create(self::NORMALISED_DATA_TABLE, function (Blueprint $table) {
            $table->charset('utf8mb4');
            $table->collation('utf8mb4_unicode_ci');
            $table->unsignedBigInteger('operation_id')->nullable();
            $table->string('date', 10)->nullable();
            $table->unsignedBigInteger('employee_contract_id')->nullable();
            $table->integer('real_duration')->nullable();
            $table->unsignedBigInteger('maintainable_id')->nullable();
            $table->string('maintainable_type', 255)->nullable();
            $table->string('shareable_group', 36)->nullable();
            $table->integer('quantity')->nullable();
        });

        $normalisedTable = self::NORMALISED_DATA_TABLE;
        $preprocessedTable = self::PREPROCESSED_DATA_TABLE;

        $sql = <<<SQL
            INSERT INTO {$normalisedTable} (
                operation_id,
                `date`,
                employee_contract_id,
                real_duration,	
                maintainable_id,
                maintainable_type,
                shareable_group,
                quantity
            )	
            SELECT
                kpd.operation_id,
                kpd.`date`,
                c.id,
                kpd.operation_duration,
                kpd.maintainable_id,
                kpd.maintainable_type,
                kpd.wtf_task_grouping_id,
                ABS(kpd.quantity)
            FROM
                {$preprocessedTable} kpd
                LEFT JOIN datahub_employee_contracts c ON c.pid = kpd.pid
            WHERE
                kpd.operation_id IS NOT NULL    
        SQL;

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

        $departmentId = self::DEPARTMENT_ID;

        $sql = <<<SQL
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
                LEFT JOIN dpb_worktimefund_model_operation o ON 
                    o.parent_id = c.id
                    AND o.deleted_at IS NULL
                LEFT JOIN dpb_departments_mm_morphable_department md ON 
                    md.morphable_id = c.id 
                    AND md.morphable_type LIKE '%Category%'
            WHERE
                md.department_id = {$departmentId}
        SQL;

        DB::statement($sql);
    }

    private function createPreprocessedDataTable(string $tableName): void
    {
        Schema::create($tableName, function (Blueprint $table) {
            $table->charset('utf8mb4');
            $table->collation('utf8mb4_unicode_ci');
            $table->string('employee_name', 255)->nullable();
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
            $table->unsignedBigInteger('operation_id')->nullable();
            $table->integer('quantity')->nullable();
            $table->unsignedBigInteger('maintainable_id')->nullable();
            $table->string('maintainable_type', 255)->nullable();
            $table->string('wtf_task_grouping_id', 36)->nullable();
        });
    }

    private function fillPreprocessedDataTable(): void
    {
        Schema::dropIfExists(self::PREPROCESSED_DATA_TABLE);
        Schema::dropIfExists(self::MAPPED_DATA_TABLE);
        Schema::dropIfExists(self::UNMAPPED_DATA_TABLE);
        

        $this->createPreprocessedDataTable(self::PREPROCESSED_DATA_TABLE);
        $this->createPreprocessedDataTable(self::MAPPED_DATA_TABLE);
        $this->createPreprocessedDataTable(self::UNMAPPED_DATA_TABLE);

        $table = self::PREPROCESSED_DATA_TABLE;
        $rawDataTable = self::RAW_DATA_TABLE;
        $mappedDataTable = self::MAPPED_DATA_TABLE;
        $unmappedDataTable = self::UNMAPPED_DATA_TABLE;

        // mapped data
        $this->preprocessMappedData($mappedDataTable, $rawDataTable);
        
        // fill table
        $sql = <<<SQL
            INSERT into {$table} 
            SELECT * FROM {$mappedDataTable}
        SQL;
        DB::statement($sql);
        
        // unmapped data
        $this->preprocessUnmappedData($unmappedDataTable, $mappedDataTable);

        $sql = <<<SQL
            INSERT into {$table} 
            SELECT * FROM {$unmappedDataTable}
        SQL;

        DB::statement($sql);


        Schema::table(self::PREPROCESSED_DATA_TABLE, function (Blueprint $table) {
            $table->index(['wtf_task_grouping_id'], 'idx_kahatova_group');
        });
    }

    private function preprocessMappedData(
        string $table,
        string $rawDataTable,
    ) {
        $baseSql = <<<SQL
            INSERT INTO `{$table}`
            SELECT distinct
                rd.`Osobné číslo`,
                SUBSTRING_INDEX(rd.`Osobné číslo`, ' - ', 1) AS pid,
                DATE_FORMAT(
                    STR_TO_DATE(rd.`Dátum`, '%e.%c.%Y'),
                    '%Y-%m-%d'
                ) AS `date`,
                vss.`code` AS vehicle_code,
                vm.title AS model,
                vss.`type`,
                rd.`Typ čistenia` AS typ_cistenia,
                o.operation as operation,
                vm.`length`,
                o.min_length,
                o.max_length,
                o.operation_duration,
                rd.`Počet pracovníkov` AS people_total,	
                o.operation_id AS operation_id,
                1 as quantity,
                v.id AS maintainable_id,
                CASE 
                    WHEN v.id IS NOT NULL THEN 'Dpb\\\\WorkTimeFund\\\\Models\\\\Maintainables\\\\Vehicle' 
                    ELSE NULL
                END AS maintainable_type,
                MD5(CONCAT_WS('|',
                    COALESCE(
                        DATE_FORMAT(
                            STR_TO_DATE(rd.`Dátum`, '%e.%c.%Y'),
                            '%Y-%m-%d'
                        ), ''),
                    COALESCE(vss.`code`, ''),
                    COALESCE(o.operation_id, '')
                )) AS wtf_task_grouping_id	
            FROM
                `{$rawDataTable}` rd 
                left join mvw_fleet_vehicle_snapshots vss ON 
                    rd.`Čislo vozidla` = vss.`code`               
                    AND rd.vehicle_model_id IS NOT NULL
                left JOIN fleet_vehicles v ON v.id = vss.vehicle_id
                left JOIN fleet_vehicle_models vm ON vm.id = v.model_id
                JOIN tmp_kahatova_operations_map o ON         
        SQL;

        // Normal operations 
        $normalCond = <<<SQL
                o.v_type = vss.`type` 
                AND o.typ_cistenia = rd.`Typ čistenia`
                AND FLOOR(vm.`length`) > o.min_length 
                AND FLOOR(vm.`length`) <= o.max_length    
            WHERE
                rd.`Typ čistenia` not IN ('Grafity', 'Predumytie vozidla')
        SQL;

        $normalQuery = $baseSql . " " . $normalCond;
        DB::statement($normalQuery);

        // Grafity operations 
        $grafityCond = <<<SQL
            o.typ_cistenia = rd.`Typ čistenia`
            AND rd.`Typ čistenia` = 'Grafity'
        SQL;
        // --            AND rd.vehicle_model_id IS NOT NULL

        $grafitylQuery = $baseSql . " " . $grafityCond;
        DB::statement($grafitylQuery);
    }

    private function preprocessUnmappedData(
        string $unmappedDataTable,
        string $mappedDataTable,
    ) {
        $operation = Operation::where('title', '9486 - import - nenamapované')->first();
        
        $departmentId = self::DEPARTMENT_ID;

        $sql = <<<SQL
            INSERT INTO `{$unmappedDataTable}` (
                `pid`,
                `date`,
                `operation_id`,
                `operation`,
                `operation_duration`,
                `quantity`,
                `wtf_task_grouping_id`
            )
            SELECT
                mdt.pid,
                mdt.`date` AS o_date,
                {$operation->id} as operation_id,
                '{$operation->title}' as operation,
                '{$operation->duration}' as operation_duration,
                FLOOR(	
                    (sum(mdt.operation_duration / mdt.people_total) - wt.shift_duration - 60) / 60
                ) AS quantity,
                UUID()
            FROM
                {$mappedDataTable} mdt
                JOIN dpb_worktimefund_model_worktime wt ON 
                    wt.personal_id = mdt.pid 
                    AND wt.date = mdt.date
            WHERE
                wt.department = {$departmentId}
                AND mdt.`date` >= '2026-07-01'
            GROUP BY 
                o_date,
                mdt.pid
            HAVING
                quantity < 0     
        SQL;

        DB::statement($sql);
    }
}
