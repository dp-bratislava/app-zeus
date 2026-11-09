<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Mappers;

use Carbon\CarbonImmutable;
use Dpb\Departments\Services\DepartmentService;
use Dpb\Modules\Tasks\Enums\TaskPlaceOfOrigin;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\DiverseTaskBatchSchema;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Dpb\Modules\Tasks\TaskBatches\Commands\CreateTaskBatchCommand;
use Dpb\Modules\Tasks\TaskBatches\Context\TaskBatchHandlerContext;
use Dpb\Package\Tasks\Models\PlaceOfOrigin;

class DiverseTaskBatchCreateFormMapper implements TaskBatchCreateFormMapper
{
    public function __construct(
        private DiverseConfigurationMapper $configMapper
    ) {}

    public function fromForm(array $data): CreateTaskBatchCommand
    {
        $taskGroupId = $data[TaskBatchForm::TASK_GROUP_PICKER_KEY] ?? null;

        $matrix = $data[DiverseTaskBatchSchema::ASSIGNMENT_MATRIX_STATE_KEY] ?? [];
        $notes = $data[DiverseTaskBatchSchema::NOTES_STATE_KEY] ?? [];

        $configuration = $this->configMapper
            ->getConfiguration(                
                $matrix,
                $notes
            );

        return new CreateTaskBatchCommand(
            // date: CarbonImmutable::parse($data['date']),
            date: CarbonImmutable::parse($data[TaskBatchForm::DATE_KEY]),
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
