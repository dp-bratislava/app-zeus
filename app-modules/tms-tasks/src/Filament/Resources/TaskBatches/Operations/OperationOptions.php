<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Operations;

use Dpb\Modules\Tasks\Eligibility\Models\DepartmentOperationRuleView;
use Dpb\WorkTimeFund\Models\Operation;
use Illuminate\Support\Facades\DB;

final class OperationOptions
{
    /**
     * @return array<int, string>
     */
    public function for(
        int $departmentId,
        int $taskItemGroupId,
    ): array {
        $dorTable = (new  DepartmentOperationRuleView)->getTable();
        $operationTable = (new  Operation)->getTable();

        return DB::table($dorTable . ' as dor') 
            ->join(
                $operationTable . ' as o',
                'o.id',
                '=',
                'dor.operation_id',
            )
            ->where('dor.department_id', '=', $departmentId)
            ->where('dor.task_item_group_id', '=', $taskItemGroupId)
            ->orderBy('o.title')
            ->get([
                'o.id as operation_id',
                'o.title as operation_title',
            ])
            ->mapWithKeys(fn ($operation) => [
                (int) $operation->operation_id => $operation->operation_title,
            ])
            ->all();            
    }
}
