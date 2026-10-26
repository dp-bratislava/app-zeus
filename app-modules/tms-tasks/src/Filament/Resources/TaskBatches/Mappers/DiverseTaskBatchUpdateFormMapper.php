<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Mappers;

use Carbon\CarbonImmutable;
use Dpb\Departments\Services\DepartmentService;
use Dpb\Modules\Tasks\Enums\TaskBatchMode;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Dpb\Modules\Tasks\Enums\TaskPlaceOfOrigin;
use Dpb\Modules\Tasks\TaskBatches\Commands\UpdateTaskBatchCommand;
use Dpb\Modules\Tasks\TaskBatches\Context\TaskBatchHandlerContext;
use Dpb\Modules\Tasks\TaskBatches\Models\TaskBatch;
use Dpb\Package\Tasks\Models\PlaceOfOrigin;

class DiverseTaskBatchUpdateFormMapper
{
    public function __construct(
        private ConfigurationMapper $configutrationMapper
    ) {}

    public function fromForm(
        TaskBatch $record,
        array $data,
    ): UpdateTaskBatchCommand {

        $matrix = $data[TaskBatchForm::FIELD_NAME_ASSIGNMENT_MATRIX_STATE] ?? [];
        $notes = $data[TaskBatchForm::FIELD_NAME_NOTES_STATE] ?? [];

        $configuration = $this->configutrationMapper
            ->getConfiguration(
                TaskBatchMode::Diverse,
                $matrix,
                $notes
            );

        return new UpdateTaskBatchCommand(
            taskBatchId: $record->id,
            date: CarbonImmutable::parse($data['date']),
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
            $groups = collect($task['task_items'] ?? [])
                ->mapWithKeys(function (array $group): array {
                    $work = collect($group['work'] ?? [])
                        ->mapWithKeys(fn(array $assignment): array => [
                            (int) $assignment['contract_id'] => [
                                'contract_id' => (int) $assignment['contract_id'],
                                'quantity' => (int) ($assignment['quantity'] ?? 1),
                            ],
                        ])
                        ->all();

                    return [
                        (int) $group['task_item_group_id'] => [
                            'work' => $work,
                        ],
                    ];
                })
                ->all();

            return [
                (int) $task['subject_id'] => $groups,
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

            TaskBatchForm::COL_NAME_CONTRACT_PICKER => $contractIds,

            TaskBatchForm::COL_NAME_TG_PICKER => $record->task_group_id,

            TaskBatchForm::FIELD_NAME_VEHICLE_PICKER => $tasks
                ->pluck('subject_id')
                ->unique()
                ->values()
                ->all(),

            TaskBatchForm::FIELD_NAME_ASSIGNMENT_MATRIX_STATE => $matrix,

            TaskBatchForm::FIELD_NAME_NOTES_STATE => $notes,
        ];
    }
}
