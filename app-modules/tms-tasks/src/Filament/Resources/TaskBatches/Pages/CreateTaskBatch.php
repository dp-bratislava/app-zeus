<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Pages;

use App\Concerns\RedirectsOnDepartmentChange;
use Dpb\Departments\Services\DepartmentService;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Mappers\TaskBatchCreateFormMapperResolver;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\DiverseTaskBatchSchema;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\TaskBatchResource;
use Dpb\Modules\Tasks\TaskBatches\Resolvers\TaskGroupModeResolver;
use Dpb\Modules\Tasks\TaskBatches\Workflows\CreateTaskBatchWorkflow;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\On;
use Override;
use RuntimeException;

class CreateTaskBatch extends CreateRecord
{
    use RedirectsOnDepartmentChange;

    protected static string $resource = TaskBatchResource::class;

    protected function handleAccessibleDepartmentChange(): void
    {
        $this->redirect(static::getResource()::getUrl('create'));
    }

    #[Override]
    protected function canAccessAfterDepartmentChange(): bool
    {
        return static::getResource()::canCreate();
    }

    public function form(Schema $schema): Schema
    {
        return TaskBatchForm::configure($schema);
    }

    protected function handleRecordCreation(array $data): Model
    {
        $taskGroupId = $data[TaskBatchForm::TASK_GROUP_PICKER_KEY];
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
        $this->data[DiverseTaskBatchSchema::ASSIGNMENT_MATRIX_STATE_KEY] = $state;
        $this->data[DiverseTaskBatchSchema::NOTES_STATE_KEY] = $notes;
    }
}
