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

        // $cfgRows = $data[TaskBatchForm::COMPONENT_NAME_TASK_BATCH_CONFIG];
        // $taskGroupId = $this->getTaskGroupId($cfgRows);
        $taskGroupId = 9;

        // $configuration = $this->getConfiguration($cfgRows);

        $matrix = $data[TaskBatchForm::FIELD_NAME_ASSIGNMENT_MATRIX_STATE] ?? [];
        $notes = $data[TaskBatchForm::FIELD_NAME_NOTES_STATE] ?? [];

        $configuration = $this->getConfiguration($matrix, $notes);

        // foreach ($cfgRows as $cfgRow) {
        //     $groups = [];
        //     foreach ($cfgRow['groups'] as $id => $value) {
        //         if ($value === true) {
        //             $groups[] = new SubjectTaskItemGroup(
        //                 taskItemGroupId: $id,
        //             );
        //         }

        //         if (is_numeric($value)) {
        //             $groups[] = new SubjectTaskItemGroup(
        //                 taskItemGroupId: $id,
        //                 quantity: $value,
        //             );
        //         }
        //     }

        //     $configs[] = new SubjectTaskConfig(
        //         subject: new TaskSubjectReference(
        //             type: EnumsTaskSubjectType::Vehicle,
        //             id: $cfgRow['vehicle_id'],
        //         ),
        //         taskItemGroups: $groups,
        //         note: $cfgRow['note'],
        //     );
        // }

        return new CreateTaskBatchCommand(
            date: CarbonImmutable::parse($data['date']),
            taskGroupId: $taskGroupId,
            configs: $configs,
            configuration: $configuration,
            context: new TaskBatchHandlerContext(
                placeOfOriginId: PlaceOfOrigin::byUri(TaskPlaceOfOrigin::Maintenance->value)->first()?->id,
                authorId: auth()->id(),
                handledAt: now()->toImmutable(),
            )
        );
    }

    private function getConfiguration(array $matrix, array $notes = []): array
    {
        return [
            'version' => 2,
            'subjects' => collect($matrix)
                ->map(fn(array $groups, int|string $subjectId) => [
                    'subject_type' => EnumsTaskSubjectType::Vehicle,
                    'subject_id' => (int) $subjectId,
                    'note' => $notes[(int) $subjectId] ?? '',
                    'groups' => collect($groups)
                        ->filter(fn(array $cell) => ! empty($cell['people']))
                        ->map(fn(array $cell, int|string $groupId) => [
                            'task_item_group_id' => (int) $groupId,
                            'people' => collect($cell['people'] ?? [])
                                ->filter(fn($person) => is_array($person)
                                    && isset($person['person_id']))
                                ->map(fn(array $person) => [
                                    'contract_id' => (int) $person['person_id'],
                                    'quantity' => (int) ($person['quantity'] ?? 1),
                                ])
                                ->values()
                                ->all(),
                        ])
                        ->values()
                        ->all(),
                ])
                ->values()
                ->all(),
        ];
    }

    private function getConfiguration1(array $data): array
    {
        return [
            'version' => 1,
            'subjects' => collect(
                $data
            )->map(fn(array $row) => [
                'subject_type' => EnumsTaskSubjectType::Vehicle,
                'subject_id' => (int) $row['vehicle_id'],
                'subject_label' => (int) $row['vehicle_label'],
                'groups' => collect($row['groups'] ?? [])
                    ->map(fn($quantity, $groupId) => [
                        'task_item_group_id' => (int) $groupId,
                        'quantity' => $quantity,
                    ])
                    ->values()
                    ->all(),
                'note' => $row['note'] ?? null,
            ])->values()->all(),
        ];
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
