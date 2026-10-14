<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Mappers;

use Carbon\CarbonImmutable;
use Dpb\Modules\Tasks\Enums\TaskPlaceOfOrigin;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Dpb\Modules\Tasks\TaskBatches\Commands\CreateTaskBatchCommand;
use Dpb\Modules\Tasks\TaskBatches\DTO\TaskSubjectReference;
use Dpb\Modules\Tasks\TaskBatches\Enums\TaskSubjectType as EnumsTaskSubjectType;
use Dpb\Modules\Tasks\TaskBatches\Context\TaskBatchHandlerContext;
use Dpb\Modules\Tasks\TaskBatches\DTO\SubjectTaskConfig;
use Dpb\Package\Tasks\Models\PlaceOfOrigin;

class TaskUniformBatchCreateFormMapper
{
    public function fromForm(array $data): CreateTaskBatchCommand
    {
        $configs = [];

        foreach ($data[TaskBatchForm::FIELD_NAME_VEHICLE_PICKER] as $subject) {
            $configs[] = new SubjectTaskConfig(
                subject: new TaskSubjectReference(
                    type: EnumsTaskSubjectType::Vehicle,
                    id: $subject,
                ),
                taskItemGroups: $data[TaskBatchForm::COL_NAME_TIG_PICKER] ?? []
            );
        }

        return new CreateTaskBatchCommand(
            date: CarbonImmutable::parse($data['date']),
            taskGroupId: $data[TaskBatchForm::COL_NAME_TG_PICKER],
            configs: $configs,
            context: new TaskBatchHandlerContext(
                placeOfOriginId: PlaceOfOrigin::byUri(TaskPlaceOfOrigin::Maintenance->value)->first()?->id,
                authorId: auth()->id(),
                handledAt: now()->toImmutable(),
            )
        );
    }
}
