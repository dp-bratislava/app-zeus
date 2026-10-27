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
use Dpb\Modules\Tasks\TaskBatches\Services\UniformConfigurationMapper;
use Dpb\Package\Tasks\Models\PlaceOfOrigin;

class UniformTaskBatchCreateFormMapper
{
    public function __construct(
        private readonly UniformConfigurationMapper $configurationMapper,
    ) {}

    public function fromForm(array $data): CreateTaskBatchCommand
    {
        $configuration = $this->configurationMapper->getConfiguration(
            subjectIds: $data[TaskBatchForm::FIELD_NAME_VEHICLE_PICKER] ?? [],
            taskGroupId: (int) $data[TaskBatchForm::COL_NAME_TG_PICKER],
            taskItemGroups: $data[TaskBatchForm::COL_NAME_TIG_PICKER] ?? [],
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
