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
        $relatedType = (new TaskItemGroup)->getMorphClass();

        return DB::
            ->join('dpb_worktimefund_mm_categoryrelations')
            ->where('related_type', '=', $relatedType)
            ->where('related_id', '=', $taskItemGroupId)
    }
}
