<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Operations;

use Dpb\Package\Tasks\Models\TaskItemGroup;
use Illuminate\Support\Facades\DB;

final class OperationOptions
{
    /**
     * @return array<int, string>
     */
    public function forTaskItemGroup(
        int $taskItemGroupId,
    ): array {
        $relatedType = TaskItemGroup::class;

        // $query = DB::table('dpb_worktimefund_model_operation as o')
        //     ->join(
        //         'dpb_worktimefund_mm_categoryrelations as cr',
        //         'o.parent_id',
        //         '=',
        //         'cr.category_id'
        //     )
        //     ->where('cr.related_type', '=', $relatedType)
        //     ->where('cr.related_id', '=', $taskItemGroupId);
        // // dd($query->toRawSql());

        // return $query
        //     ->pluck('o.title', 'o.id')
        //     ->mapWithKeys(fn($label, $id) => [
        //         (int) $id => $label,
        //     ])
        //     ->all();

        return DB::table('dpb_worktimefund_model_operation as o')
            ->join(
                'dpb_worktimefund_mm_categoryrelations as cr',
                'o.parent_id',
                '=',
                'cr.category_id',
            )
            ->where('cr.related_type', TaskItemGroup::class)
            ->where('cr.related_id', $taskItemGroupId)
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
