<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Pages;

use Dpb\Departments\Services\DepartmentService;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Actions\BackToViewAction;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Mappers\DiverseTaskBatchUpdateFormMapper;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Mappers\TaskBatchUpdateFormMapperResolver;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\DiverseTaskBatchForm;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\TaskBatchResource;
use Dpb\Modules\Tasks\TaskBatches\Resolvers\TaskGroupModeResolver;
use Dpb\Modules\Tasks\TaskBatches\Workflows\UpdateTaskBatchWorkflow;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\On;

class EditTaskBatch extends EditRecord
{
    protected static string $resource = TaskBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            BackToViewAction::make($this->record),
            DeleteAction::make(),
        ];
    }

    public function form(Schema $schema): Schema
    {
        // return DiverseTaskBatchForm::configure($schema);
        return TaskBatchForm::configure($schema);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $taskGroupId = $data['task_group_id'];
        $departmentId = app(DepartmentService::class)->getActiveDepartment()->id;

        $mode = app(TaskGroupModeResolver::class)
            ->resolve($taskGroupId, $departmentId);

        $mapper = app(TaskBatchUpdateFormMapperResolver::class)
            ->resolve($mode);

        // $mapper = app(DiverseTaskBatchUpdateFormMapper::class);        
        
        return $mapper->fromDatabase($this->record);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $taskGroupId = $data[TaskBatchForm::COL_NAME_TG_PICKER];
        $departmentId = app(DepartmentService::class)->getActiveDepartment()->id;

        $mode = app(TaskGroupModeResolver::class)
            ->resolve($taskGroupId, $departmentId);

        $mapper = app(TaskBatchUpdateFormMapperResolver::class)
            ->resolve($mode);

        // $mapper = app(DiverseTaskBatchUpdateFormMapper::class);
        $workflow = app(UpdateTaskBatchWorkflow::class);

        $command = $mapper->fromForm($record, $data);
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
