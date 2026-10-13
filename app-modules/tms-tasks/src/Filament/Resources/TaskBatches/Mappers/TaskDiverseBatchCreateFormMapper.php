<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Mappers;

use Carbon\CarbonImmutable;
use Dpb\Modules\Tasks\Enums\TaskPlaceOfOrigin;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Dpb\Modules\Tasks\TaskBatches\Commands\CreateTaskBatchCommand;
use Dpb\Modules\Tasks\TaskBatches\DTO\SubjectTaskConfig;
use Dpb\Modules\Tasks\TaskBatches\DTO\SubjectTaskItemGroup;
use Dpb\Modules\Tasks\TaskBatches\DTO\TaskSubjectReference;
use Dpb\Modules\Tasks\TaskBatches\Enums\TaskSubjectType as EnumsTaskSubjectType;
use Dpb\Modules\Tasks\TaskBatches\Context\TaskBatchHandlerContext;
use Dpb\Package\Tasks\Models\PlaceOfOrigin;
use Dpb\Package\Tasks\Models\TaskGroup;
use Dpb\Package\Tasks\Models\TaskItemGroup;

class TaskDiverseBatchCreateFormMapper
{
    public function fromForm(array $data): CreateTaskBatchCommand
    {
        $configs = [];

        $cfgRows = $data[TaskBatchForm::COMPONENT_NAME_TASK_BATCH_CONFIG];
        $taskGroupId = $this->getTaskGroupId($cfgRows);

        foreach ($cfgRows as $cfgRow) {
            $groups = [];
            foreach ($cfgRow['groups'] as $id => $value) {
                if ($value === true) {
                    $groups[] = new SubjectTaskItemGroup(
                        taskItemGroupId: $id,
                    );
                }

                if (is_numeric($value)) {
                    $groups[] = new SubjectTaskItemGroup(
                        taskItemGroupId: $id,
                        quantity: $value,
                    );
                }
            }

            $configs[] = new SubjectTaskConfig(
                subject: new TaskSubjectReference(
                    type: EnumsTaskSubjectType::Vehicle,
                    id: $cfgRow['vehicle_id'],
                ),
                taskItemGroups: $groups,
                note: $cfgRow['note'],
            );
        }

        return new CreateTaskBatchCommand(
            date: CarbonImmutable::parse($data['date']),
            taskGroupId: $taskGroupId,
            configs: $configs,
            context: new TaskBatchHandlerContext(
                placeOfOriginId: PlaceOfOrigin::byUri(TaskPlaceOfOrigin::Maintenance->value)->first()?->id,
                authorId: auth()->id(),
                handledAt: now()->toImmutable(),
            )
        );
    }

    private function getTaskGroupId(array $data): ?int
    {
        if (empty($data)) {
            return null;
        }

        $taskGroupId = TaskItemGroup::where('id', '=', array_key_first($data[0]['groups']))?->first()?->task_group_id;

        if ($taskGroupId === null) {
            return null;
        }

        return TaskGroup::where('id', '=', $taskGroupId)?->first()?->id;
    }
}
