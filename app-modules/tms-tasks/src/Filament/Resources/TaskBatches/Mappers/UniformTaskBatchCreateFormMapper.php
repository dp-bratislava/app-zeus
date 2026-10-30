<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Mappers;

use Carbon\CarbonImmutable;
use Dpb\Departments\Services\DepartmentService;
use Dpb\Modules\Tasks\Enums\TaskPlaceOfOrigin;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Mappers\UniformConfigurationMapper;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Dpb\Modules\Tasks\TaskBatches\Commands\CreateTaskBatchCommand;
use Dpb\Modules\Tasks\TaskBatches\Context\TaskBatchHandlerContext;
use Dpb\Package\Tasks\Models\PlaceOfOrigin;

class UniformTaskBatchCreateFormMapper implements TaskBatchCreateFormMapper
{
    public function __construct(
        private readonly UniformConfigurationMapper $configurationMapper,
    ) {}

    public function fromForm(array $data): CreateTaskBatchCommand
    {
        $configuration = $this->configurationMapper->getConfiguration(
            subjects: $data[TaskBatchForm::FIELD_NAME_VEHICLE_PICKER] ?? [],
            selectedTaskItemGroupIds: $data[TaskBatchForm::COL_NAME_TIG_PICKER] ?? [],
            operationsByGroup: $data[TaskBatchForm::COL_NAME_OPERATION_PICKER] ?? [],
            contractIds: $data[TaskBatchForm::COL_NAME_CONTRACT_PICKER] ?? [],
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
}
