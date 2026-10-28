<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Mappers;

use Carbon\CarbonImmutable;
use Dpb\Departments\Services\DepartmentService;
use Dpb\Modules\Tasks\Enums\TaskPlaceOfOrigin;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\UniformTaskBatchForm;
use Dpb\Modules\Tasks\TaskBatches\Commands\UpdateTaskBatchCommand;
use Dpb\Modules\Tasks\TaskBatches\Context\TaskBatchHandlerContext;
use Dpb\Modules\Tasks\TaskBatches\Models\TaskBatch;
use Dpb\Package\Tasks\Models\PlaceOfOrigin;

class UniformTaskBatchUpdateFormMapper
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
                subjects: $data[UniformTaskBatchForm::FIELD_NAME_VEHICLE_PICKER] ?? [],
                taskGroupId: (int) $data[UniformTaskBatchForm::COL_NAME_TG_PICKER],
                selectedTaskItemGroupIds: $data[UniformTaskBatchForm::COL_NAME_TIG_PICKER] ?? [],
                operationsByGroup: $data[UniformTaskBatchForm::COL_NAME_OPERATION_PICKER] ?? [],
                contractIds: $data[UniformTaskBatchForm::COL_NAME_CONTRACT_PICKER] ?? [],
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

        $taskItemGroups = $configuration['task_item_groups'] ?? [];

        return [
            'date' => $record->date,

            UniformTaskBatchForm::COL_NAME_CONTRACT_PICKER =>
            array_values(array_unique(
                $configuration['contract_ids'] ?? []
            )),

            UniformTaskBatchForm::COL_NAME_TG_PICKER =>
            $configuration['task_group_id'] ?? null,

            UniformTaskBatchForm::COL_NAME_TIG_PICKER =>
            array_map('intval', array_keys($taskItemGroups)),

            UniformTaskBatchForm::COL_NAME_OPERATION_PICKER =>
            collect($taskItemGroups)
                ->map(
                    fn(array $operationIds): array =>
                    array_map('intval', $operationIds)
                )
                ->all(),

            UniformTaskBatchForm::FIELD_NAME_VEHICLE_PICKER =>
            array_map(
                'intval',
                $configuration['subjects']['vehicle'] ?? []
            ),
        ];
    }
}
