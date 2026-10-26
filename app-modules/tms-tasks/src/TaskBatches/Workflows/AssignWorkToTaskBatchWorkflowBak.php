<?php

namespace Dpb\Modules\Tasks\TaskBatches\Workflows;

use Dpb\DatahubSync\Models\EmployeeContract;
use Dpb\Modules\Tasks\Eligibility\AvailableOperations;
use Dpb\Modules\Tasks\TaskBatches\Resolvers\WtfActivityMaintainableResolver;
use Exception;
use Illuminate\Support\Facades\DB;
use Dpb\WorkTimeFund\Models\ActivityRecord;
use Dpb\WorkTimeFund\Models\Task as WtfTask;
use Dpb\Modules\Tasks\TaskBatches\Commands\AssignWorkToTaskBatchCommand;
use Dpb\Modules\Tasks\TaskBatches\Resolvers\TaskAssignmentSubjectResolver;
use Dpb\WorkTimeFund\Models\Operation;
use Dpb\WorkTimeFund\Models\WorkTime;
use Dpb\WtfTmsBridge\Models\WorkOrder;
use Illuminate\Support\Collection;

class AssignWorkToTaskBatchWorkflow
{
    // set all activities as fulfileld by default 
    private const DEFAULT_IS_FULFILLED = true;
    private const DEFAULT_QUANTITY_MULTIPLIER = 1;

    // set all activities as operations by default
    private const DEFAULT_ACTIVITY_TYPE = 'O';
    private const DEFAULT_WTF_TASK_STATUS = 'completed';
    // minimal activity duration limit in seconds
    // so that sharable activities don't get zeroed out
    private const MINIMAL_DURATION_LIMIT = 60;

    public function __construct(
        private AvailableOperations $availableOperations,
        private TaskAssignmentSubjectResolver $subjectResolver,
        private WtfActivityMaintainableResolver $maintainableResolver,
    ) {}

    public function execute(
        AssignWorkToTaskBatchCommand $command,
    ) {
        try {
            $result = DB::transaction(function () use ($command) {
                $workorderIdsByTaskItemIds = [];
                $taskIds = [];
                $activityIds = [];

                // assign task batch items 
                // $workorderIds = $this->assignTaskBatchItems();
                // create wtf workorders
                $workorderIdsByTaskItemIds = $this->createWorkorders($command);

                // create wtf tasks, workorder tasks mm binding 
                // and wtf activity records
                $wtfTaskIds = $this->createWtfTasks(
                    $command,
                    $workorderIdsByTaskItemIds
                );

                // attach batch records
                // if ($batch !== null) {
                //     $batch->attachRecordIds(
                //         app(ActivityRecord::class)
                //             ->getMorphClass(),
                //         $activityIds
                //     );
                // }
            });
        } catch (Exception $e) {
            dd($e);
        }

        return $result;
    }

    /**
     * @TODO
     * @return Collection
     */
    private function createWorkorders(
        AssignWorkToTaskBatchCommand $command
    ): Collection {

        $taskItemIds = $command->taskItemConfigs
            ->pluck('taskItemId')
            ->unique()
            ->mapWithKeys(fn(int $id) => [
                $id => ['tms_task_item_id' => $id],
            ])
            ->all();

        WorkOrder::insert($taskItemIds);

        return WorkOrder::whereIn('tms_task_item_id', $taskItemIds)
            ->pluck('id', 'tms_task_item_id');
    }

    /**
     * @TODO
     * @return array
     */
    private function createWtfTasks(
        AssignWorkToTaskBatchCommand $command,
        Collection $workorderIdsByTaskItemIds
    ): array {
        // map workorders
        $workorderWtfTasks = [];
        $activities = [];
        $now = now();

$contractIds = $command->taskItemConfigs
    ->flatMap(fn ($config) => $config->operationIds)
    ->flatMap(fn ($operation) => $operation->work)
    ->pluck('contractId')
    ->unique()
    ->values();

        $contractPids = EmployeeContract::query()
            ->whereIn('id', $contractIds)
            ->pluck('pid', 'id')
            ->all();

        $worktimeIds = WorkTime::query()
            ->whereIn('personal_id', array_values($contractPids))
            ->where('date', '=', $command->date)
            ->pluck('id', 'personal_id')
            ->all();

        foreach ($command->taskItemConfigs as $taskItemConfig) {
            // $operations = $this->availableOperations->for(
            //     $taskItemConfig->taskItemGroupId,
            //     $this->subjectResolver->resolve($taskItemConfig->subject)
            // );
            $operations = Operation::query()
                ->whereIn('id', $taskItemConfig->operationIds->pluck('operationId'))
                ->get();

            $maintainable = $this->maintainableResolver
                ->resolve($taskItemConfig->subject);
            // logger(
            //     'operacie: ',
            //     $operations->toArray()
            // );

            foreach ($operations as $operation) {
    $operationConfig = $operationConfigs->get($operation->id);

    $work = $operationConfig->work;

                // wtf tasks
                $wtfTask = WtfTask::create([
                    'source_id' => $operation->id,
                    'title' => $operation->title,
                    'expected_duration' => $operation->duration,
                    'is_shareable' => $operation->is_shareable,
                    'status' => self::DEFAULT_WTF_TASK_STATUS,
                    'department_id' => $command->context->departmentId,
                    'maintainable_type' => $maintainable::class,
                    'maintainable_id' => $maintainable->id,
                ]);

                // map
                $workorderWtfTasks[] = [
                    'workorder_id' => $workorderIdsByTaskItemIds[$taskItemConfig->taskItemId],
                    'taskitem_id' => $wtfTask->id,
                ];

                // activity records
$peopleCount = $operationConfig->work
    ->pluck('contractId')
    ->unique()
    ->count();

                foreach ($taskItemConfig->operationIds as $work) {
                    $pid = $contractPids[$work->contractId];

                    // TODO
                    // handle worktimes that dont exist
                    if (isset($worktimeIds[$pid]) === false) {
                        // logger('pridaj pracu, nema zmenu PID: ' . $pid);
                        continue;
                    }

                    $duration = max(
                        $work->quantity * $operation->duration / (
                            $operation->is_shareable
                            ? $peopleCount
                            : self::DEFAULT_QUANTITY_MULTIPLIER
                        ),
                        self::MINIMAL_DURATION_LIMIT
                    );

                    $activities[] = [
                        'title' => $operation->title,
                        'type' => self::DEFAULT_ACTIVITY_TYPE,
                        'expected_duration' => $duration,
                        'real_duration' => $duration,
                        'is_official' => $operation->is_official,
                        'is_fulfilled' => self::DEFAULT_IS_FULFILLED,
                        'date' => $command->date,

                        'personal_id' => $pid,
                        'department_id' => $command->context->departmentId,

                        'parent_id' => $worktimeIds[$pid], // worktime
                        'source_id' => $operation->id,
                        'task_id' => $wtfTask->id,
                        'quantity' => $work->quantity,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        // DB::table('dpb_wtftmsbridge_mm_workorder_task')
        //     ->insert($workorderWtfTasks);

        // ActivityRecord::insert($activities);

        foreach (array_chunk($workorderWtfTasks, 500) as $chunk) {
            DB::table('dpb_wtftmsbridge_mm_workorder_task')
                ->insert($chunk);
        }

        foreach (array_chunk($activities, 500) as $chunk) {
            ActivityRecord::insert($chunk);
        }

        return [];
    }
}
