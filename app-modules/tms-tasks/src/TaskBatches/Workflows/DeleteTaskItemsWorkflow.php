<?php

namespace Dpb\Modules\Tasks\Workflows;

use Dpb\Package\TaskMS\Models\TaskItemAssignment;
use Dpb\Package\Tasks\Models\TaskItem;
use Dpb\WorkTimeFund\Models\ActivityRecord as WtfActivityRecord;
use Dpb\WorkTimeFund\Models\Task as WtfTask;
use Dpb\\WorkOrder;
use Illuminate\Support\Facades\DB;

class DeleteTaskItemsWorkflow
{
    private const MM_TABLE = 'dpb_wtftmsbridge_mm_workorder_task';

    /**
     * @ todo
     * task item table column name fix
     *
     * @param  array $taskItemIds
     */
    public function handle(array $taskItemIds)
    {
        if (DB::transactionLevel() > 0) {
            // Already in a parent transaction
            return $this->deleteTaskItems($taskItemIds);
        } else {
            // Open new transaction
            DB::transaction(function () use ($taskItemIds) {
                return $this->deleteTaskItems($taskItemIds);
            });
        }
    }

    private function deleteTaskItems(array $taskItemIds)
    {
        $workOrderIds = WorkOrder::whereIn('tms_task_item_id', $taskItemIds)
            ->pluck('id');

        $wtfTaskIds = DB::table(self::MM_TABLE)
            ->whereIn('workorder_id', $workOrderIds)
            ->pluck('taskitem_id');

        // delete activities
        WtfActivityRecord::whereIn('task_id', $wtfTaskIds)
            ->delete();

        // delete wtf pivot data
        DB::table(self::MM_TABLE)
            ->whereIn('workorder_id', $workOrderIds)
            ->delete();

        // delete wtf tasks
        WtfTask::whereIn('id', $wtfTaskIds)
            ->delete();

        // delete wtf work orders
        WorkOrder::whereIn('id', $workOrderIds)
            ->delete();

        // delete tms task item assignment
        TaskItemAssignment::whereIn('task_item_id', $taskItemIds)
            ->delete();

        // delete tms task item
        TaskItem::whereIn('id', $taskItemIds)
            ->delete();
    }
}
