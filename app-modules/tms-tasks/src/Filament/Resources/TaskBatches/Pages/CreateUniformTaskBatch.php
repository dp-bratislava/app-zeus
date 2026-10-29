<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Pages;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Mappers\UniformTaskBatchCreateFormMapper;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\UniformTaskBatchForm;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\TaskBatchResource;
use Dpb\Modules\Tasks\TaskBatches\Workflows\CreateTaskBatchWorkflow;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class CreateUniformTaskBatch extends CreateRecord
{
    protected static string $resource = TaskBatchResource::class;
    // protected string $view = 'filament.resources.task-batches.pages.create-uniform-task-batch';

    public function form(Schema $schema): Schema
    {
        return UniformTaskBatchForm::configure($schema);
    }

    protected function handleRecordCreation(array $data): Model
    {
        $mapper = app(UniformTaskBatchCreateFormMapper::class);
        $workflow = app(CreateTaskBatchWorkflow::class);

        $command = $mapper->fromForm($data);
        $result = $workflow->execute($command);


        // dd($result);
        return $result->taskBatch;
    }
}
