<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Pages;

use Dpb\Departments\Services\DepartmentService;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Mappers\DiverseTaskBatchCreateFormMapper;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Mappers\TaskBatchCreateFormMapperResolver;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\DiverseTaskBatchForm;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\TaskBatchResource;
use Dpb\Modules\Tasks\TaskBatches\Resolvers\TaskGroupModeResolver;
use Dpb\Modules\Tasks\TaskBatches\Workflows\CreateTaskBatchWorkflow;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\On;

class CreateTaskBatch extends CreateRecord
{
    protected static string $resource = TaskBatchResource::class;

    public function form(Schema $schema): Schema
    {
        return TaskBatchForm::configure($schema);
    }

    protected function handleRecordCreation(array $data): Model
    {
        $taskGroupId = $data[TaskBatchForm::COL_NAME_TG_PICKER];
        $departmentId = app(DepartmentService::class)->getActiveDepartment()->id;

        $mode = app(TaskGroupModeResolver::class)
            ->resolve($taskGroupId, $departmentId);

        $mapper = app(TaskBatchCreateFormMapperResolver::class)
            ->resolve($mode);

        $workflow = app(CreateTaskBatchWorkflow::class);

        $command = $mapper->fromForm($data);
        $result = $workflow->execute($command);

        return $result->taskBatch;
    }

    #[On('assignment-matrix-updated')]
    public function updateAssignmentMatrix(
        array $state,
        array $notes = []
    ): void {
        $this->data[DiverseTaskBatchForm::FIELD_NAME_ASSIGNMENT_MATRIX_STATE] = $state;
        $this->data[DiverseTaskBatchForm::FIELD_NAME_NOTES_STATE] = $notes;
    }
}
