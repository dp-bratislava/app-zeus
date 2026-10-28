<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Pages;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Actions\BackToViewAction;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Mappers\DiverseTaskBatchUpdateFormMapper;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Mappers\UniformTaskBatchUpdateFormMapper;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\UniformTaskBatchForm;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\TaskBatchResource;
use Dpb\Modules\Tasks\TaskBatches\Workflows\UpdateTaskBatchWorkflow;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\On;

class EditUniformTaskBatch extends EditRecord
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
        return UniformTaskBatchForm::configure($schema);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $mapper = app(UniformTaskBatchUpdateFormMapper::class);        
        
        return $mapper->fromDatabase($this->record);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $mapper = app(UniformTaskBatchUpdateFormMapper::class);
        $workflow = app(UpdateTaskBatchWorkflow::class);

        $command = $mapper->fromForm($record, $data);
        $result = $workflow->execute($command);


        // dd($result);
        return $result->taskBatch;
    }

    #[On('assignment-matrix-updated')]
    public function updateAssignmentMatrix(
        array $state,
        array $notes = []
    ): void {
        $this->data[TaskBatchForm::FIELD_NAME_ASSIGNMENT_MATRIX_STATE] = $state;
        $this->data[TaskBatchForm::FIELD_NAME_NOTES_STATE] = $notes;

        // logger()->debug('Assignment matrix updated', [
        //     'state' => $state,
        // ]);
    }
}
