<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Pages;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Mappers\DiverseTaskBatchCreateFormMapper;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\TaskBatchResource;
use Dpb\Modules\Tasks\TaskBatches\Workflows\CreateTaskBatchWorkflow;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\On;

class CreateTaskBatch extends CreateRecord
{
    protected static string $resource = TaskBatchResource::class;

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
        $this->data[TaskBatchForm::FIELD_NAME_ASSIGNMENT_MATRIX_STATE] = $state;
        $this->data[TaskBatchForm::FIELD_NAME_NOTES_STATE] = $notes;

        // logger()->debug('Assignment matrix updated', [
        //     'state' => $state,
        // ]);
    }
}
