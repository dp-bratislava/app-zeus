<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Mappers;

use Carbon\CarbonImmutable;
use Dpb\Package\Tasks\Models\PlaceOfOrigin;
use Dpb\Modules\Tasks\Commands\UpdateTaskBatchCommand;
use Dpb\Modules\Tasks\TaskBatches\Context\TaskBatchHandlerContext;
use Dpb\Modules\Tasks\DTO\TaskSubjectReference;
use Dpb\Modules\Tasks\Enums\TaskPlaceOfOrigin;
use Dpb\Modules\Tasks\Enums\TaskSubjectType;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Dpb\Modules\Tasks\Models\TaskBatch;

class TaskBatchUpdateFormMapper
{
    public function fromForm(
        array $data,
        TaskBatch $record
    ): UpdateTaskBatchCommand {
        return new UpdateTaskBatchCommand(
            taskBatchId: $record->id,
            date: CarbonImmutable::parse($data['date']),
            taskGroupId: $data[TaskBatchForm::COL_NAME_TG_PICKER],
            subjects: array_map(
                fn(int|string $id) => new TaskSubjectReference(
                    type: TaskSubjectType::Vehicle,
                    id: $id,
                ),
                $data[TaskBatchForm::COL_NAME_VEHICLE_PICKER] ?? [],
            ),
            taskItemGroupIds: array_map(
                'intval',
                $data[TaskBatchForm::COL_NAME_TIG_PICKER] ?? [],
            ),
            context: new TaskBatchHandlerContext(
                placeOfOriginId: PlaceOfOrigin::byUri(TaskPlaceOfOrigin::Maintenance->value)->first()?->id,
                authorId: auth()->id(),
                handledAt: now()->toImmutable(),
            )
        );
    }
}
