<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Mappers;

use Carbon\CarbonImmutable;
use Dpb\Departments\Services\DepartmentService;
use Dpb\Modules\Tasks\Enums\TaskPlaceOfOrigin;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\DiverseTaskBatchForm;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Dpb\Modules\Tasks\TaskBatches\Commands\CreateTaskBatchCommand;
use Dpb\Modules\Tasks\TaskBatches\Context\TaskBatchHandlerContext;
use Dpb\Package\Tasks\Models\PlaceOfOrigin;
use Dpb\Package\Tasks\Models\TaskGroup;
use Dpb\Package\Tasks\Models\TaskItemGroup;

class DiverseTaskBatchCreateFormMapper implements TaskBatchCreateFormMapper
{
    public function __construct(
        private DiverseConfigurationMapper $configMapper
    ) {}

    public function fromForm(array $data): CreateTaskBatchCommand
    {
        $taskGroupId = $data[TaskBatchForm::COL_NAME_TG_PICKER] ?? null;

        $matrix = $data[DiverseTaskBatchForm::FIELD_NAME_ASSIGNMENT_MATRIX_STATE] ?? [];
        $notes = $data[DiverseTaskBatchForm::FIELD_NAME_NOTES_STATE] ?? [];

        $configuration = $this->configMapper
            ->getConfiguration(                
                $matrix,
                $notes
            );

        return new CreateTaskBatchCommand(
            // date: CarbonImmutable::parse($data['date']),
            date: CarbonImmutable::parse($data[TaskBatchForm::COL_NAME_DATE]),
            taskGroupId: $taskGroupId,
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
