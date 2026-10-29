<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Pages;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Mappers\DiverseTaskBatchCreateFormMapper;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\DiverseTaskBatchForm;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\TaskBatchResource;
use Dpb\Modules\Tasks\TaskBatches\Workflows\CreateTaskBatchWorkflow;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\On;

class CreateDiverseTaskBatch extends CreateRecord
{
    // public function mount(): void
    // {
    //     dd([
    //         'page_reached' => true,
    //         'url' => request()->fullUrl(),
    //         'user_id' => auth()->id(),
    //     ]);
    // }

    protected static string $resource = TaskBatchResource::class;
    // protected string $view = 'filament.resources.task-batches.pages.create-diverse-task-batch';

    public function form(Schema $schema): Schema
    {
        return DiverseTaskBatchForm::configure($schema);
    }

    protected function handleRecordCreation(array $data): Model
    {
        $mapper = app(DiverseTaskBatchCreateFormMapper::class);
        $workflow = app(CreateTaskBatchWorkflow::class);

        $command = $mapper->fromForm($data);
        $result = $workflow->execute($command);


        // dd($result);
        return $result->taskBatch;
    }

    #[On('assignment-matrix-updated')]
    public function updateAssignmentMatrix(
        array $state,
        array $notes = []
    ): void {
        $this->data[DiverseTaskBatchForm::FIELD_NAME_ASSIGNMENT_MATRIX_STATE] = $state;
        $this->data[DiverseTaskBatchForm::FIELD_NAME_NOTES_STATE] = $notes;

        // logger()->debug('Assignment matrix updated', [
        //     'state' => $state,
        // ]);
    }
}
