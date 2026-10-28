<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Operations;

use Dpb\Modules\Tasks\Eligibility\AvailableOperations;
use Dpb\Modules\Tasks\TaskBatches\DTO\TaskSubjectReference;
use Dpb\Modules\Tasks\TaskBatches\Enums\TaskSubjectType;
use Dpb\Modules\Tasks\TaskBatches\Resolvers\TaskAssignmentSubjectResolver;
use Dpb\Package\Tasks\Models\TaskItemGroup;
use Illuminate\Support\Facades\DB;

final class OperationOptions
{
    public function __construct(
        private readonly AvailableOperations $availableOperations,
        private readonly TaskAssignmentSubjectResolver $subjectResolver,
    ) {}

    /**
     * @return array<int, string>
     */
    public function forTaskItemGroup(
        int $taskItemGroupId,
    ): array {
        // $relatedType = (new TaskItemGroup)->getMorphClass();
        $relatedType = TaskItemGroup::class;

        // return DB::table('dpb_worktimefund_model_operation as o')
        $query = DB::table('dpb_worktimefund_model_operation as o')
            ->join(
                'dpb_worktimefund_mm_categoryrelations as cr',
                'o.parent_id',
                '=',
                'cr.category_id'
            )
            ->where('cr.related_type', '=', $relatedType)
            ->where('cr.related_id', '=', $taskItemGroupId);
        // dd($query->toRawSql());

        return $query
            ->pluck('o.title', 'o.id')
            ->mapWithKeys(fn($label, $id) => [
                (int) $id => $label,
            ])
            ->all();
    }
}
