<?php

namespace Dpb\Modules\Tasks\Observers;

use Carbon\Carbon;
use Dpb\DatahubSync\Models\EmployeeContract;
use Dpb\Package\Tasks\Models\TaskItem;
use Dpb\WorkTimeFund\Models\ActivityRecord;
use Dpb\WorkTimeFund\Models\Operation;
use Dpb\WorkTimeFund\Models\WorkTime;
use Dpb\Modules\Tasks\Services\TaskService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;

class TaskItemObserver
{
    public function created(
        TaskItem $taskItem
    ): void {
        // dd($this->getRequest());
        try {
            $taskService = App::make(abstract: TaskService::class);
            foreach ($this->getRequest()['data']['mountedTableActionsData'][0][0][0]['wtf_tasks'][0] ?? [] as $taskData) {
                $operation = Operation::findOrFail(id: $taskData[0]['operation_id'] ?? null);
                $task = $taskService->createWtfTaskFromTmsTaskItemAndOperation(
                    taskItem: $taskItem,
                    operation: $operation
                );
                foreach ($taskData[0]['wtf_activities'][0] ?? [] as $activityData) {
                    $timeFrom = Carbon::parse($activityData[0]['date'].' '.$activityData[0]['time_from'] ?? null);
                    $timeTo = Carbon::parse($activityData[0]['date'].' '.$activityData[0]['time_to'] ?? null);
                    $realDuration = $timeFrom->diffInSeconds($timeTo);
                    $employeeContract = EmployeeContract::find(id: $activityData[0]['employee_contract_id'] ?? null);
                    $worktime = WorkTime::query()
                        ->where(
                            column: 'personal_id',
                            operator: '=',
                            value: $employeeContract->pid
                        )
                        ->where(
                            column: 'date',
                            operator: '=',
                            value: $activityData[0]['date'] ?? null
                        )
                        ->firstOrFail();
                    ActivityRecord::create(
                        attributes: [
                            'title' => $task->title,
                            'type' => 'O',
                            'expected_duration' => $task->expected_duration,
                            'real_duration' => $realDuration,
                            'is_official' => $operation->is_official,
                            'date' => $activityData[0]['date'] ?? null,
                            'is_fulfilled' => -1,
                            'start' => $timeFrom->format('Y-m-d H:i:s'),
                            'end' => $timeTo->format('Y-m-d H:i:s'),
                            'personal_id' => $employeeContract->pid,
                            'department_id' => $task->department_id,
                            'source_id' => $operation->id,
                            'parent_id' => $worktime->id,
                            'sorting' => 1,
                            'task_id' => $task->id,
                        ]
                    );
                }
            }
        } catch (\Exception $e) {
            // dd($activityData[0]);
        }
    }

    public function updated(
        TaskItem $taskItem
    ): void {
        // dd($taskItem, $this->getRequest());
    }

    private function getRequest(): array
    {
        $snapshot = Request::all()['components'][0]['snapshot'] ?? '';

        return json_decode(
            json: $snapshot,
            associative: true
        ) ?? [];
    }
}
