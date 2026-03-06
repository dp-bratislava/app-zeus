<?php
require '../../vendor/autoload.php';

use Dpb\DatahubSync\Models\EmployeeContract;
use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\WorkTimeFund\Models\ActivityRecord;
use Dpb\WorkTimeFund\Models\Category;
use Dpb\WorkTimeFund\Models\Operation;
use Dpb\WorkTimeFund\Models\WorkTime;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

try {
    $start = microtime(as_float: true);
    $app = require_once '../../bootstrap/app.php';
    $kernel = $app->make(Kernel::class);
    $kernel->handle(Request::capture());

    App::make(abstract: OperationsImport::class)->run();
    App::make(abstract: ActivityImport::class)->run();
    dd('Import tooks ' . (microtime(as_float: true) - $start) . ' seconds.');
} catch(Exception $ex) {
    dd($ex);
}

class ActivityImport {

    private const PATH_TO_ARES_DATA = 'http://ares.dpb.sk/wtf/aresexport.php?dateFrom=2025-01-01';
    //private const PATH_TO_ARES_DATA = 'ares_export.json';

    private array $aresData;

    private array $worktimes = [];

    private array $vehicles = [];

    private array $employeeContracts = [];

    private array $oldOperationIds = [];

    public function __construct(
        protected readonly ConnectionInterface $connection
    ) {}

    public function run(): void {
        $this
            ->loadOldOperationIds()
            ->clearData()
            ->loadDataFromAres()
            ->loadEmployeeContracts()
            ->loadVehicles()
            ->loadWorktimes()
            ->handleData();
    }

    private function clearData(): static {
        DB::table(table: 'dpb_worktimefund_model_activityrecord')
            ->where(
                column: 'department_id',
                operator: '=',
                value: 70
            )
            ->delete();

        DB::table(table: 'dpb_worktimefund_model_task')
            ->where(
                column: 'department_id',
                operator: '=',
                value: 70
            )
            ->delete();

        DB::table(table: 'dpb_worktimefund_model_worktime')
            ->where(
                column: 'department',
                operator: '=',
                value: 70
            )
            ->delete();

        return $this;
    }

    private function loadOldOperationIds(): static {
        $this->oldOperationIds = $this->connection
            ->table(table: 'dpb_aresimport_operations')
            ->get()
            ->mapWithKeys(
                callback: fn(
                    stdClass $value
                ): array => [
                    $value->ares_id => $value->zeus_id
                ]
            )
            ->toArray();
        return $this;
    }

    private function getOperationIdInZeus(
        int $aresId
    ): int {
        if($aresId === 0) {
            return 0;
        }
        return $this->oldOperationIds[$aresId]
            ?? throw new Exception(message: "Import failed for: $aresId!");
    }

    private function handleData(): static {
        DB::transaction(callback: function(): void {
            foreach(array_chunk(array: $this->aresData, length: 1000) as $chunk) {
                $tasksToInsert = array_map(
                    callback: function(
                        array $item
                    ): array {
                        $taskData = [
                            'source_id' => $this->getOperationIdInZeus(aresId: $item['source_id']),
                            'title' => $item['title'],
                            'expected_duration' => max($item['expected_duration'], 0),
                            'is_shareable' => 0,
                            'status' => 'started',
                            'department_id' => $item['department_id']
                        ];
                        if(!empty($item['vehicle_code'])) {
                            $taskData['maintainable_type'] = 'Dpb\WorkTimeFund\Models\Maintainables\Vehicle';
                            $taskData['maintainable_id'] = $this->getVehicleIdByCode(code: $item['vehicle_code']);
                        } else {
                            $taskData['maintainable_type'] = '';
                            $taskData['maintainable_id'] = null;
                        }
                        return $taskData;
                    },
                    array: $chunk
                );

                DB::table(table: 'dpb_worktimefund_model_task')
                    ->insert(values: $tasksToInsert);
                $firstTaskId = DB::getPdo()->lastInsertId();
                
                $activitiesToInsert = [];
                foreach($chunk as $index => $item) {
                    $activitiesToInsert[] = [
                        'title' => $item['title'],
                        'type' => $item['type'],
                        'expected_duration' => $item['expected_duration'],
                        'real_duration' => $item['real_duration'],
                        'is_official' => $item['is_official'],
                        'is_fulfilled' => $item['is_fulfilled'],
                        'date' => $item['date'],
                        'personal_id' => $item['personal_id'],
                        'department_id' => $item['department_id'],
                        'source_id' => $this->getOperationIdInZeus(aresId: $item['source_id']),
                        'parent_id' => $this->getWorktime(activityRecordData: $item)?->id ?? 0,
                        'task_id' => $firstTaskId + $index
                    ];
                }
                DB::table(table: 'dpb_worktimefund_model_activityrecord')
                    ->insert(values: $activitiesToInsert);
            }
        });
        return $this;
    }

    private function loadDataFromAres(): static {
        $this->aresData = json_decode(
            json: file_get_contents(filename: self::PATH_TO_ARES_DATA),
            associative: true);
        return $this;
    }

    private function loadEmployeeContracts(): static {
        $this->employeeContracts = EmployeeContract::query()
            ->with(relations: ['employee'])
            ->whereIn(column: 'pid', values: $this->getUniqueValuesOfAresDataColumn(column: 'personal_id'))
            ->get()
            ->mapWithKeys(callback: fn(EmployeeContract $employeeContract): array => [$employeeContract->pid => $employeeContract])
            ->all();
        return $this;
    }

    private function loadVehicles(): static {
        $this->vehicles = Vehicle::query()
            ->with(relations: ['codes'])
            ->get()
            ->mapWithKeys(callback: fn(Vehicle $vehicle): array => [$vehicle->code?->code ?? 'N/A' => $vehicle->id])
            ->toArray();
        return $this;
    }

    private function loadWorktimes(): static {
        $demandedPersonalIds = $this->getUniqueValuesOfAresDataColumn(column: 'personal_id');
        $demandedDates = $this->getUniqueValuesOfAresDataColumn(column: 'date');

        $worktimes = WorkTime::query()
            ->with(relations: ['activityRecords'])
            ->whereIn(column: 'date', values: $demandedDates)
            ->whereIn(column: 'personal_id', values: $demandedPersonalIds)
            ->get();

        //[L:] check existing activity record
        $existingActivityRecords = ActivityRecord::query()
            ->whereIn(
                column: 'parent_id',
                values: $worktimes->pluck(value: 'id')
            )
            ->get();
        
        if($existingActivityRecords->count() > 0) {
            dd($existingActivityRecords);
        }

        $this->worktimes = $worktimes
                ->mapWithKeys(callback: fn(WorkTime $worktime): array => [$worktime->date . '|' . $worktime->personal_id => $worktime])
                ->all();
        return $this;
    }

    private function getEmployeeContract(
        int $personalId
    ): EmployeeContract {
        return $this->employeeContracts[$personalId] ?? null;
    }

    private function getWorktime(
        array $activityRecordData
    ): WorkTime {
        $demandedWorktime =  $this->worktimes[$activityRecordData['date'] . '|' . $activityRecordData['personal_id']] ?? null;
        if(empty($demandedWorktime)) {
            $demandedWorktime = WorkTime::create(attributes: [
                'date' => $activityRecordData['date'],
                'first_name' => $this->getEmployeeContract(personalId: $activityRecordData['personal_id'])->employee->first_name ?? '',
                'last_name' => $this->getEmployeeContract(personalId: $activityRecordData['personal_id'])->employee->last_name ?? '',
                'personal_id' => $activityRecordData['personal_id'],
                'shift' => $this->getShiftIdByShiftDuration(duration: $activityRecordData['shift_duration']),
                'shift_start' => $activityRecordData['shift_start'],
                'shift_duration' => $activityRecordData['shift_duration'],
                'department' => $activityRecordData['department_id'],
                'min_cataloging_quota' => 97
            ]);
            $this->worktimes[$demandedWorktime->date . '|' . $demandedWorktime->personal_id] = $demandedWorktime;
        }
        return $demandedWorktime;
    }

    private function getVehicleIdByCode(
        int $code
    ): ?int {
        return $this->vehicles[$code] ?? null;
    }

    private function getShiftIdByShiftDuration(
        int $duration
    ): int {
        return match($duration) {
            41400 => 2,
            27000 => 35,
            default => 1
        };
    }

    private function getUniqueValuesOfAresDataColumn(
        string $column,
        bool $trimEmpty = true
    ): array {
        $uniqueValues = array_unique(
            array: array_column(
                array: $this->aresData, 
                column_key: $column
            )
        );
        return $trimEmpty ? array_filter(array: $uniqueValues) : $uniqueValues;
    }
}


class OperationsImport {

    private const PATH_TO_ARES_DATA = 'http://ares.dpb.sk/wtf/operations.php';
    private const ARES_REFERENCES_TABLE = 'dpb_aresimport_operations';

    private array $aresOperations = [];

    private array $processedCategories = [];

    private array $activityIdReferences = [];

    public function __construct(
        protected readonly ConnectionInterface $connection
    ) {}

    public function run(): void {
        $this->prepareReferenceToOldIdTable();
        $this->connection
            ->transaction(
                callback: function(): void {
                    $this
                        ->removeOldOperations()
                        ->loadOperationsFromAres()
                        ->importOperations()
                        ->storeOldActivityReferences();
                }
            );
    }

    private function removeOldOperations(): static {
        $categories = Category::query()
            ->with(relations: ['departments', 'descendants.operations', 'operations'])
            ->whereHas(relation: 'departments', callback: function(Builder $query): void {
                $query->whereIn(column: 'department_id', values: [70]);
            })
            ->get();

        /** @var Category $category */
        foreach($categories as $category) {
            if($category->departments->count() === 1) {
                $this->removeCategoryAndItsOperations(category: $category);
            } else {
                $category->departments()->detach(ids: 70);
            }
        }
        return $this;
    }

    private function removeCategoryAndItsOperations(
        Category $category
    ): void {
        if(in_array(needle: $category->id, haystack: $this->processedCategories)) {
            return;
        }
    
        $this->processedCategories[] = $category->id;

        $category->operations()->delete();

        /** @var Category $descendant */
        foreach($category->descendants as $descendant) {
            $this->removeCategoryAndItsOperations(category: $descendant);
        }

        $category->departments()->detach();
        $category->delete();
    }

    private function prepareReferenceToOldIdTable(): static {
        if(!Schema::hasTable(table: self::ARES_REFERENCES_TABLE)) {
            Schema::create(
                table: self::ARES_REFERENCES_TABLE,
                callback: function(
                    Blueprint $table
                ): void {
                    $table->unsignedBigInteger(column: 'ares_id');
                    $table->unsignedBigInteger(column: 'zeus_id');
                }
            );
        } else {
            $this->connection->table(self::ARES_REFERENCES_TABLE)->truncate();
        }
        return $this;
    }

    private function loadOperationsFromAres(): static {
        $this->aresOperations = json_decode(
            json: file_get_contents(filename: self::PATH_TO_ARES_DATA),
            associative: true);
        return $this;
    }

    private function importOperations(): static {
        foreach($this->aresOperations as $categoryData) {
            $this->createCategory(categoryData: $categoryData);
        }
        return $this;
    }

    private function createCategory(
        array $categoryData,
        ?int $parentId = null
    ): void {
        /** @var Category $category */
        $category = Category::create(attributes: [
            'title' => $categoryData['title'],
            'parent_id' => $parentId,
            'is_official' => $categoryData['is_official']
        ]);

        $category->type = $categoryData['type'];
        $category->save();

        $category
            ->departments()
            ->attach(ids: 70);

        $category->syncVehicles(vehicleModelIds: array_column(array: $categoryData['vehicles'], column_key: 'id_foreign') ?? []);

        foreach($categoryData['descendants'] ?? [] as $descendantData) {
            $this->createCategory(
                categoryData: $descendantData,
                parentId: $category->id
            );
        }

        foreach($categoryData['operations'] ?? [] as $operationData) {
            $operation = Operation::create(attributes: [
                'title' => $operationData['title'],
                'duration' => $operationData['duration'],
                'parent_id' => $category->id,
                'is_official' => $operationData['is_official'],
                'is_shareable' => 0
            ]);
            $this->activityIdReferences[] = [
                'ares_id' => $operationData['id'],
                'zeus_id' => $operation->id
            ];
        }
    }

    private function storeOldActivityReferences(): static {
        $this->connection
            ->table(table: self::ARES_REFERENCES_TABLE)
            ->insert(values: $this->activityIdReferences);
        return $this;
    }
}