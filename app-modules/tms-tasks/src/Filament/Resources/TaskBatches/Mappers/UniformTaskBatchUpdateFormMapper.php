<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Mappers;

use Carbon\CarbonImmutable;
use Dpb\Departments\Services\DepartmentService;
use Dpb\Modules\Tasks\Enums\TaskPlaceOfOrigin;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\UniformTaskBatchSchema;
use Dpb\Modules\Tasks\TaskBatches\Commands\UpdateTaskBatchCommand;
use Dpb\Modules\Tasks\TaskBatches\Context\TaskBatchHandlerContext;
use Dpb\Modules\Tasks\TaskBatches\Models\TaskBatch;
use Dpb\Package\Tasks\Models\PlaceOfOrigin;

class UniformTaskBatchUpdateFormMapper implements TaskBatchUpdateFormMapper
{
    public function __construct(
        private UniformConfigurationMapper $configMapper
    ) {}

    public function fromForm(
        TaskBatch $record,
        array $data,
    ): UpdateTaskBatchCommand {

        $configuration = $this->configMapper
            ->getConfiguration(
                subjects: $data[TaskBatchForm::SUBJECT_PICKER_KEY] ?? [],
                selectedTaskItemGroupIds: $data[UniformTaskBatchSchema::TIG_PICKER_KEY] ?? [],
                operationsByGroup: $data[UniformTaskBatchSchema::OPERATION_PICKER_KEY] ?? [],
                contractIds: $data[TaskBatchForm::CONTRACT_PICKER_KEY] ?? [],
            );

        return new UpdateTaskBatchCommand(
            taskBatchId: $record->id,
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

        $taskItemGroups = $configuration['task_item_groups'] ?? [];

        return [
            TaskBatchForm::DATE_KEY =>
            $record->date,

            TaskBatchForm::CONTRACT_PICKER_KEY =>
            array_values(array_unique(
                $configuration['contract_ids'] ?? []
            )),

            TaskBatchForm::TASK_GROUP_PICKER_KEY =>
            $record->taskGroup->id ?? null,

            UniformTaskBatchSchema::TIG_PICKER_KEY =>
            array_map('intval', array_keys($taskItemGroups)),

            UniformTaskBatchSchema::OPERATION_PICKER_KEY =>
            collect($taskItemGroups)
                ->map(
                    fn(array $operationIds): array =>
                    array_map('intval', $operationIds)
                )
                ->all(),

            TaskBatchForm::SUBJECT_PICKER_KEY =>
            array_map(
                'intval',
                $configuration['subjects']['vehicle'] ?? []
            ),
        ];
    }
}
