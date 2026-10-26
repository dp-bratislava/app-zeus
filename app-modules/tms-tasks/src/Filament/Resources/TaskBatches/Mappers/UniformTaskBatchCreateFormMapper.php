<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Mappers;

use Carbon\CarbonImmutable;
use Dpb\Departments\Services\DepartmentService;
use Dpb\Modules\Tasks\Enums\TaskBatchMode;
use Dpb\Modules\Tasks\Enums\TaskPlaceOfOrigin;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Dpb\Modules\Tasks\TaskBatches\Commands\CreateTaskBatchCommand;
use Dpb\Modules\Tasks\TaskBatches\DTO\TaskSubjectReference;
use Dpb\Modules\Tasks\TaskBatches\Enums\TaskSubjectType as EnumsTaskSubjectType;
use Dpb\Modules\Tasks\TaskBatches\Context\TaskBatchHandlerContext;
use Dpb\Modules\Tasks\TaskBatches\DTO\SubjectTaskConfig;
use Dpb\Package\Tasks\Models\PlaceOfOrigin;

class UniformTaskBatchCreateFormMapper
{
    private const VERSION = '1';

    public function fromForm(array $data): CreateTaskBatchCommand
    {
        $matrix = $data[TaskBatchForm::FIELD_NAME_ASSIGNMENT_MATRIX_STATE] ?? [];
        $notes = $data[TaskBatchForm::FIELD_NAME_NOTES_STATE] ?? [];

        $configuration = $this->getConfiguration(
            TaskBatchMode::Uniform,
            $matrix,
            $notes
        );

        return new CreateTaskBatchCommand(
            date: CarbonImmutable::parse($data['date']),
            taskGroupId: $data[TaskBatchForm::COL_NAME_TG_PICKER],
            configuration: $configuration,
            context: new TaskBatchHandlerContext(
                placeOfOriginId: PlaceOfOrigin::byUri(TaskPlaceOfOrigin::Maintenance->value)->first()?->id,
                authorId: auth()->id(),
                handledAt: now()->toImmutable(),
                departmentId: app(DepartmentService::class)->getActiveDepartment()->id
            )
        );
    }

    public function getConfiguration(
        TaskBatchMode $mode,
        array $matrix,
        array $notes = []
    ): array {
        return [
            'version' => self::VERSION,
            'mode' => $mode->value,
            'tasks' => collect($matrix)
                ->map(fn(array $groups, int|string $subjectId) => [
                    // TODO
                    'task_group_id' => 9,

                    'subject_type' => EnumsTaskSubjectType::Vehicle->value,
                    'subject_id' => (int) $subjectId,
                    'note' => $notes[(int) $subjectId] ?? '',
                    'task_item_groups' => collect($groups)
                        ->filter(fn(array $cell) => ! empty($cell['work']))
                        ->map(fn(array $cell, int|string $groupId) => [
                            'task_item_group_id' => (int) $groupId,
                            'work' => collect($cell['work'] ?? [])
                                ->filter(fn($work) => is_array($work)
                                    && isset($work['contract_id']))
                                ->map(fn(array $work) => [
                                    'contract_id' => (int) $work['contract_id'],
                                    'quantity' => (int) ($work['quantity'] ?? 1),
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
}
