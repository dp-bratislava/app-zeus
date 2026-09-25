<?php

namespace Dpb\Modules\Tasks\Workflows;

use Dpb\Package\TaskMS\Handlers\Task\CreateTaskHandler;
use Dpb\Package\TaskMS\Handlers\TaskAssignment\CreateTaskAssignmentHandler;
use Dpb\Package\TaskMS\Handlers\TaskItem\CreateTaskItemHandler;
use Dpb\Package\TaskMS\Handlers\TaskItemAssignment\CreateTaskItemAssignmentHandler;
use Dpb\Package\Tasks\Models\TaskItem;
use Exception;
use Illuminate\Support\Facades\DB;
use Dpb\WorkTimeFund\Models\ActivityRecord;
use Dpb\WorkTimeFund\Models\Operation;
use Dpb\WorkTimeFund\Models\Task;
use Dpb\Modules\Tasks\Commands\AssignWorkToTaskBatchCommand;
use Dpb\Modules\Tasks\DTO\TaskBatchWorkList;
use Dpb\WtfUi\FilamentComponents\AssignmentContainer\AssignmentRedisService;

class AssignWorkToTaskBatchWorkflow
{
    public function __construct(
        private AssignmentRedisService $svc
    ) {}

    public function handle(
        AssignWorkToTaskBatchCommand $command,
    ) {
        $result = null;
        $assignmentRequests = $this->svc->get();
        // dd($assignmentRequests);

        $data = $this->preprocessData();

        dd($data);

        try {
            $result = DB::transaction(function () use ($command, $data) {
                $workorderIds = [];
                $taskIds = [];
                $activityIds = [];

                // assign task batch items 
                $workorderIds = $this->assignTaskBatchItems();
                // create wtf workorders
                $workorderIds = $this->createWorkorders();
                // create wtf tasks
                $taskIds = $this->createTasks();
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
    private function preprocessData(): TaskBatchWorkList
    {
        $assignmentRequests = $this->svc->get();

        $contracts = [];
        $operationIds = [];
        $wortimes = [];

        foreach ($assignmentRequests as $request) {
            $operationIds[] = $request->activityDefinition->modelId;
        }

        $operations = Operation::query()
            ->whereIn('id', collect($operationIds)->unique())
            ->get([
                'id',
                'title',
                'duration',
                'is_shareable'
            ])
            ->keyBy('id');

        return new TaskBatchWorkList(
            $contracts,
            $operations,
            $wortimes,
        );
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
     * @return array
     */
    private function createWorkorders(): array
    {
        return [];
    }

    /**
     * @TODO
     * @return array
     */
    private function createTasks(): array
    {
        $data = [];
        $ids = [];

        foreach ($data as $record) {
            $ids[] = Task::create($record);
        }
        return $ids;
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
