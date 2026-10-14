<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Mappers;

use Carbon\CarbonImmutable;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Dpb\Modules\Tasks\Enums\TaskPlaceOfOrigin;
use Dpb\Modules\Tasks\TaskBatches\Commands\UpdateTaskUniformBatchCommand;
use Dpb\Modules\Tasks\TaskBatches\Context\TaskBatchHandlerContext;
use Dpb\Modules\Tasks\TaskBatches\DTO\TaskSubjectReference;
use Dpb\Modules\Tasks\TaskBatches\Enums\TaskSubjectType;
use Dpb\Modules\Tasks\TaskBatches\Models\TaskBatch;
use Dpb\Package\Tasks\Models\PlaceOfOrigin;

class TaskDiverseBatchUpdateFormMapper
{
    public function fromForm(
        array $data,
        TaskBatch $record
    ): UpdateTaskUniformBatchCommand {
        return new UpdateTaskUniformBatchCommand(
            taskBatchId: $record->id,
            date: CarbonImmutable::parse($data['date']),
            taskGroupId: $data[TaskBatchForm::COL_NAME_TG_PICKER],
            subjects: array_map(
                fn(int|string $id) => new TaskSubjectReference(
                    type: TaskSubjectType::Vehicle,
                    id: $id,
                ),
                $data[TaskBatchForm::FIELD_NAME_VEHICLE_PICKER] ?? [],
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

    public function fromDatabase(
        TaskBatch $record,
    ): array {
        $subjects = collect(
            data_get($record->configuration, 'subjects', [])
        );

        return [
            // date
            'date' => $record->date,

            // task group
            TaskBatchForm::COL_NAME_TG_PICKER => $record->taskGroup->id,

            // vehicles
            TaskBatchForm::FIELD_NAME_VEHICLE_PICKER => $subjects
                ->pluck('subject_id')
                ->values()
                ->all(),

            // configs
            TaskBatchForm::COMPONENT_NAME_TASK_BATCH_CONFIG => $subjects
                ->map(function (array $subject) {
                    return [
                        'vehicle_id' => $subject['subject_id'],
                        'vehicle_label' => $subject['subject_label'],
                        'note' => $subject['note'] ?? null,

                        'groups' => collect($subject['groups'] ?? [])
                            ->mapWithKeys(fn(array $group) => [
                                $group['task_item_group_id'] => $group['quantity'],
                            ])
                            ->all(),
                    ];
                })
                ->values()
                ->all(),
        ];
    }
}
