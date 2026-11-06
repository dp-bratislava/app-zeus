<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Pages;

use App\Concerns\RedirectsOnDepartmentChange;
use Dpb\Departments\Services\DepartmentService;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Actions\BackToViewAction;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Mappers\TaskBatchUpdateFormMapperResolver;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\DiverseTaskBatchSchema;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\TaskBatchResource;
use Dpb\Modules\Tasks\TaskBatches\Resolvers\TaskGroupModeResolver;
use Dpb\Modules\Tasks\TaskBatches\Workflows\UpdateTaskBatchWorkflow;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\On;
use Override;
use RuntimeException;

class EditTaskBatch extends EditRecord
{
    use RedirectsOnDepartmentChange;

    protected static string $resource = TaskBatchResource::class;

    protected function getDepartmentChangeRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    #[Override]
    protected function canStillAccessPage(): bool
    {
        return static::getResource()::canViewAny();
    }

    protected function getHeaderActions(): array
    {
        return [
            BackToViewAction::make($this->record),
            DeleteAction::make(),
        ];
    }

    public function form(Schema $schema): Schema
    {
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

        return $mapper->fromDatabase($this->record);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $taskGroupId = $data[TaskBatchForm::TASK_GROUP_PICKER_KEY];
        $departmentId = app(DepartmentService::class)->getActiveDepartment()->id;

        $mode = app(TaskGroupModeResolver::class)
            ->resolve($taskGroupId, $departmentId);

        $mapper = app(TaskBatchUpdateFormMapperResolver::class)
            ->resolve($mode);

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
        $this->data[DiverseTaskBatchSchema::ASSIGNMENT_MATRIX_STATE_KEY] = $state;
        $this->data[DiverseTaskBatchSchema::NOTES_STATE_KEY] = $notes;
    }
}
