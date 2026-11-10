<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Mappers;

use Carbon\CarbonImmutable;
use Dpb\Departments\Services\DepartmentService;
use Dpb\Modules\Tasks\Enums\TaskPlaceOfOrigin;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\DiverseTaskBatchSchema;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Dpb\Modules\Tasks\TaskBatches\Commands\UpdateTaskBatchCommand;
use Dpb\Modules\Tasks\TaskBatches\Context\TaskBatchHandlerContext;
use Dpb\Modules\Tasks\TaskBatches\Models\TaskBatch;
use Dpb\Package\Tasks\Models\PlaceOfOrigin;

class DiverseTaskBatchUpdateFormMapper implements TaskBatchUpdateFormMapper
{
    public function __construct(
        private DiverseConfigurationMapper $configMapper
    ) {}

    public function fromForm(
        TaskBatch $record,
        array $data,
    ): UpdateTaskBatchCommand {

        $matrix = $data[DiverseTaskBatchSchema::ASSIGNMENT_MATRIX_STATE_KEY] ?? [];
        $notes = $data[DiverseTaskBatchSchema::NOTES_STATE_KEY] ?? [];

        $configuration = $this->configMapper
            ->getConfiguration(
                $matrix,
                $notes
            );

        return new UpdateTaskBatchCommand(
            taskBatchId: $record->id,
            // date: CarbonImmutable::parse($data['date']),
            date: CarbonImmutable::parse($data[TaskBatchForm::DATE_KEY]),
            configuration: $configuration,
            context: new TaskBatchHandlerContext(
                placeOfOriginId: PlaceOfOrigin::byUri(TaskPlaceOfOrigin::Maintenance->value)->first()?->id,
                authorId: auth()->id(),
                handledAt: now()->toImmutable(),
                departmentId: app(DepartmentService::class)->getActiveDepartment()->id
            )
        );
    }

    public function fromDatabase(TaskBatch $record): array
    {
        $configuration = $record->configuration ?? [];

        if (is_string($configuration)) {
            $configuration = json_decode($configuration, true) ?? [];
        }

        $tasks = collect($configuration['tasks'] ?? []);

        $matrix = $tasks->mapWithKeys(function (array $task): array {
            $taskItems = collect($task['task_items'] ?? [])
                ->mapWithKeys(function (array $taskItem): array {
                    $work = collect($taskItem['work'] ?? [])
                        ->mapWithKeys(fn(array $assignment): array => [
                            (int) $assignment['contract_id'] => [
                                'contract_id' => (int) $assignment['contract_id'],
                                'quantity' => (int) ($assignment['quantity'] ?? 1),
                            ],
                        ])
                        ->all();

                    return [
                        (int) $taskItem['task_item_group_id'] => [
                            'work' => $work,
                        ],
                    ];
                })
                ->all();

            return [
                (int) $task['subject_id'] => $taskItems,
            ];
        })->all();

        $notes = $tasks->mapWithKeys(fn(array $task): array => [
            (int) $task['subject_id'] => $task['note'] ?? '',
        ])->all();

        $contractIds = $tasks
            ->flatMap(
                fn(array $task) => collect($task['task_items'] ?? [])
                    ->flatMap(
                        fn(array $group) => collect($group['work'] ?? [])
                            ->pluck('contract_id')
                    )
            )
            ->unique()
            ->values()
            ->all();

        return [
            'date' => $record->date,

            TaskBatchForm::CONTRACT_PICKER_KEY => $contractIds,

            TaskBatchForm::TASK_GROUP_PICKER_KEY => $record->task_group_id,

            TaskBatchForm::SUBJECT_PICKER_KEY => $tasks
                ->pluck('subject_id')
                ->unique()
                ->values()
                ->all(),

            DiverseTaskBatchSchema::ASSIGNMENT_MATRIX_STATE_KEY => $matrix,

            DiverseTaskBatchSchema::NOTES_STATE_KEY => $notes,
        ];
    }
}
