<?php

namespace Dpb\Modules\Tasks\TaskBatches\Workflows;

use Dpb\Package\TaskMS\Handlers\Task\CreateTaskHandler;
use Dpb\Package\TaskMS\Handlers\TaskAssignment\CreateTaskAssignmentHandler;
use Dpb\Package\TaskMS\Handlers\TaskItem\CreateTaskItemHandler;
use Dpb\Package\TaskMS\Handlers\TaskItemAssignment\CreateTaskItemAssignmentHandler;
use Dpb\Package\Tasks\Models\TaskItem;
use Exception;
use Illuminate\Support\Facades\DB;
use Dpb\WorkTimeFund\Models\ActivityRecord;
use Dpb\WorkTimeFund\Models\Operation;
use Dpb\WorkTimeFund\Models\Task as WtfTask;
use Dpb\Modules\Tasks\TaskBatches\Commands\AssignWorkToTaskBatchCommand;
use Dpb\Modules\Tasks\TaskBatches\DTO\TaskBatchWorkList;
use Dpb\Modules\Tasks\TaskBatches\DTO\WtfTaskItemConfig;
use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\WtfTmsBridge\Models\WorkOrder;
use Dpb\WtfUi\FilamentComponents\AssignmentContainer\AssignmentRedisService;
use Illuminate\Support\Collection;

class AssignWorkToTaskBatchWorkflow
{
    private const DEFAULT_IS_FULFILLED = true;

    public function handle(
        AssignWorkToTaskBatchCommand $command,
    ) {
        try {
            $result = DB::transaction(function () use ($command) {
                $workorderIds = [];
                $taskIds = [];
                $activityIds = [];

                // assign task batch items 
                $workorderIds = $this->assignTaskBatchItems();
                // create wtf workorders
                $workorderIds = $this->createWorkorders($command);
                // create wtf tasks
                $wtfTaskIds = $this->createWtfTasks();
                // create wtf workorder tasks mm binding

                // create wtf activity records
                $activityIds = $this->createActivities();
                // create wtf mandatory break activities

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
     * @return array
     */
    private function assignTaskBatchItems(): array
    {
        return [];
    }

    /**
     * @TODO
     * @return Collection
     */
    private function createWorkorders(
        AssignWorkToTaskBatchCommand $command
    ): Collection {

        $taskItemIds = $command->taskItemConfigs
            ->flatMap(fn(WtfTaskItemConfig $config) => $config->taskItemIds)
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
        Collection $workorderIds
    ): array {
        // create wtf tasks
        $wtfTasks = [];
        $now = now();

        $taskItemGroupIds = $command->taskItemConfigs
            ->pluck('taskItemGroupId')
            ->unique()
            ->values()
            ->all();

        $contractPids = [];    

        foreach ($command->taskItemConfigs as $taskItemConfig) {
            $taskItemConfig->taskItemGroupId;
            // $taskItemConfig->work;



            // $operations = Operation::whereIn('id', $operationIds)
            // ->
            foreach ($operations[$taskItemConfig->taskItemGroupId] as $operation) {
                $wtfTask = WtfTask::create([
                    'source_id' => $operation->id,
                    'title' => $operation->title,
                    'expected_duration' => $operation->expected_duration,
                    'is_shareable' => $operation->is_shareable,
                    'status' => 'created',
                    'department_id' => $command->context->departmentId,
                    // TODO
                    // 'maintainable_type' => $taskItemConfig->subject->type->value,
                    'maintainable_type' => Vehicle::class,
                    'maintainable_id' => $taskItemConfig->subject->id,
                ]);

                foreach ($taskItemConfig->work as $person) {
                    $activities[] = [
                        'title' => $operation->title,
                        'type' => 'O',
                        'expected_duration' => $operation->expected_duration,
                        'real_duration' => $operation->expected_duration,
                        'is_official' => $operation->is_official,
                        'is_fulfilled' => self::DEFAULT_IS_FULFILLED,
                        'date' => $command->date,

                        'personal_id' => $contractPids[$person->contract_id],
                        'department_id' => $command->context->departmentId,

                        'parent_id' => null, // worktime
                        'task_id' => $wtfTask->id,
                        'quantity' => $person->quantity,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        WtfTask::insert($wtfTasks);

        // map workorders
        $workorderTasks = [];

        DB::table('dpb_wtftmsbridge_mm_workorder_task')
            ->insert($workorderTasks);

        return [];
    }

    /**
     * @TODO
     * @return array
     */
    private function createActivities(): array
    {
        return [];
    }

    /**
     * @TODO
     * @return array
     */
    private function createBreaks(): array
    {
        return [];
    }
}
