<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Mappers;

use Carbon\CarbonImmutable;
use Dpb\Modules\Tasks\Enums\TaskPlaceOfOrigin;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Dpb\Modules\Tasks\TaskBatches\Commands\CreateTaskDiverseBatchCommand;
use Dpb\Modules\Tasks\TaskBatches\Commands\CreateTaskUniformBatchCommand;
use Dpb\Modules\Tasks\TaskBatches\DTO\SubjectTaskConfig;
use Dpb\Modules\Tasks\TaskBatches\DTO\SubjectTaskItemGroup;
use Dpb\Modules\Tasks\TaskBatches\DTO\TaskSubjectReference;
use Dpb\Modules\Tasks\TaskBatches\Enums\TaskSubjectType as EnumsTaskSubjectType;
use Dpb\Modules\Tasks\TaskBatches\Context\TaskBatchHandlerContext;
use Dpb\Package\Tasks\Models\PlaceOfOrigin;

class TaskDiverseBatchCreateFormMapper
{
    public function fromForm(array $data): CreateTaskDiverseBatchCommand
    {
        $configs = [];

        $cfgRows = $data[TaskBatchForm::COMPONENT_NAME_TASK_BATCH_CONFIG];

        foreach ($cfgRows as $cfgRow) {
            $groups = [];
            foreach ($cfgRow['groups'] as $id => $value) {
                if ($value === true) {
                    $groups[] = new SubjectTaskItemGroup(
                        taskItemGroupId: $id
                    );
                }

                if (is_numeric($value)) {
                    $groups[] = new SubjectTaskItemGroup(
                        taskItemGroupId: $id,
                        quantity: $value
                    );
                }
            }
            $configs[] = new SubjectTaskConfig(
                subjectType: EnumsTaskSubjectType::Vehicle,
                subjectId: $cfgRow['vehicle_id'],
                taskItemGroups: $groups,
                note: $cfgRow['note'],
            );
        }

        return new CreateTaskDiverseBatchCommand(
            date: CarbonImmutable::parse($data['date']),
            configs: $configs,
            context: new TaskBatchHandlerContext(
                placeOfOriginId: PlaceOfOrigin::byUri(TaskPlaceOfOrigin::Maintenance->value)->first()?->id,
                authorId: auth()->id(),
                handledAt: now()->toImmutable(),
            )
        );
    }
}
