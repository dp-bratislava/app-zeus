<?php

namespace Dpb\Modules\Tasks\TaskBatches\Workflows;

use App\Models\Datahub\EmployeeContract;
use Dpb\Modules\Tasks\TaskBatches\Commands\AssignWorkToTaskBatchCommand;
use Dpb\Modules\Tasks\TaskBatches\Resolvers\WtfActivityMaintainableResolver;
use Dpb\Modules\Tasks\TaskBatches\DTO\TaskBatchTaskItemConfig;
use Dpb\Modules\Tasks\TaskBatches\DTO\WtfTaskItemConfig;
use Dpb\WorkTimeFund\Models\ActivityRecord;
use Dpb\WorkTimeFund\Models\Operation;
use Dpb\WorkTimeFund\Models\Task as WtfTask;
use Dpb\WorkTimeFund\Models\WorkTime;
use Dpb\WtfTmsBridge\Models\WorkOrder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class AssignWorkToTaskBatchWorkflow
{
    private const DEFAULT_IS_FULFILLED = true;
    private const DEFAULT_QUANTITY_MULTIPLIER = 1;
    private const DEFAULT_ACTIVITY_TYPE = 'O';
    private const DEFAULT_WTF_TASK_STATUS = 'completed';
    private const MINIMAL_DURATION_LIMIT = 60;
    private const INSERT_CHUNK_SIZE = 500;

    public function __construct(
        private readonly WtfActivityMaintainableResolver $maintainableResolver,
    ) {}

    public function execute(
        AssignWorkToTaskBatchCommand $command,
    ): array {
        return DB::transaction(function () use ($command): array {
            $workorderIdsByTaskItemIds = $this->createWorkorders($command);

            return $this->createWtfTasks(
                $command,
                $workorderIdsByTaskItemIds,
            );
        });
    }

    /**
     * @return Collection<int, int> Keyed by task item ID, with work order IDs as values.
     */
    private function createWorkorders(
        AssignWorkToTaskBatchCommand $command,
    ): Collection {
        $taskItemIds = $command->taskItemConfigs
            ->pluck('taskItemId')
            ->unique()
            ->values();

        if ($taskItemIds->isEmpty()) {
            return collect();
        }

        $rows = $taskItemIds
            ->map(fn (int $id): array => [
                'tms_task_item_id' => $id,
            ])
            ->all();

        foreach (array_chunk($rows, self::INSERT_CHUNK_SIZE) as $chunk) {
            WorkOrder::insert($chunk);
        }

        return WorkOrder::query()
            ->whereIn('tms_task_item_id', $taskItemIds)
            ->pluck('id', 'tms_task_item_id');
    }

    /**
     * @param AssignWorkToTaskBatchCommand $command
     * @param Collection<int, int> $workorderIdsByTaskItemIds
     * @return array<int, int> Created WTF task IDs.
     */
    private function createWtfTasks(
        AssignWorkToTaskBatchCommand $command,
        Collection $workorderIdsByTaskItemIds,
    ): array {
        $now = now();

        $work = $command->taskItemConfigs
            ->flatMap(fn (WtfTaskItemConfig $config) => $config->work);

        $contractIds = $work
            ->pluck('contractId')
            ->unique()
            ->values();

        $contractPids = EmployeeContract::query()
            ->whereIn('id', $contractIds)
            ->pluck('pid', 'id');

        $worktimeIds = WorkTime::query()
            ->whereIn('personal_id', $contractPids->values())
            ->whereDate('date', $command->date)
            ->pluck('id', 'personal_id');

        $workorderWtfTasks = [];
        $activities = [];
        $wtfTaskIds = [];

        foreach ($command->taskItemConfigs as $taskItemConfig) {
            $operationIds = collect($taskItemConfig->operationIds)
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            if ($operationIds->isEmpty()) {
                continue;
            }

            $operations = Operation::query()
                ->whereIn('id', $operationIds)
                ->get();

            $maintainable = $this->maintainableResolver
                ->resolve($taskItemConfig->subject);

            $workorderId = $workorderIdsByTaskItemIds->get(
                $taskItemConfig->taskItemId,
            );

            if ($workorderId === null) {
                throw new \RuntimeException(
                    "Work order not found for task item {$taskItemConfig->taskItemId}.",
                );
            }

            // Work is defined once per task-item group and applies to each operation.
            $groupWork = $taskItemConfig->work;

            foreach ($operations as $operation) {
                $wtfTask = WtfTask::create([
                    'source_id' => $operation->id,
                    'title' => $operation->title,
                    'expected_duration' => $operation->duration,
                    'is_shareable' => $operation->is_shareable,
                    'status' => self::DEFAULT_WTF_TASK_STATUS,
                    'department_id' => $command->context->departmentId,
                    'maintainable_type' => $maintainable->getMorphClass(),
                    'maintainable_id' => $maintainable->getKey(),
                ]);

                $wtfTaskIds[] = $wtfTask->id;

                $workorderWtfTasks[] = [
                    'workorder_id' => $workorderId,
                    'taskitem_id' => $wtfTask->id,
                ];

                $peopleCount = $groupWork
                    ->pluck('contractId')
                    ->unique()
                    ->count();

                foreach ($groupWork as $workItem) {
                    $pid = $contractPids->get($workItem->contractId);

                    // Skip assignments with missing contracts or work times.
                    if ($pid === null) {
                        continue;
                    }

                    $worktimeId = $worktimeIds->get($pid);

                    if ($worktimeId === null) {
                        continue;
                    }

                    $divisor = $operation->is_shareable
                        ? max($peopleCount, 1)
                        : self::DEFAULT_QUANTITY_MULTIPLIER;

                    $duration = max(
                        $workItem->quantity * $operation->duration / $divisor,
                        self::MINIMAL_DURATION_LIMIT,
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
                        'parent_id' => $worktimeId,
                        'source_id' => $operation->id,
                        'task_id' => $wtfTask->id,
                        'quantity' => $workItem->quantity,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        foreach (
            array_chunk($workorderWtfTasks, self::INSERT_CHUNK_SIZE)
            as $chunk
        ) {
            DB::table('dpb_wtftmsbridge_mm_workorder_task')->insert($chunk);
        }

        foreach (
            array_chunk($activities, self::INSERT_CHUNK_SIZE)
            as $chunk
        ) {
            ActivityRecord::insert($chunk);
        }

        return $wtfTaskIds;
    }
}