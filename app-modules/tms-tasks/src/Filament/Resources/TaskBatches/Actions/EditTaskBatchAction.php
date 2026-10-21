<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Actions;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Mappers\DiverseTaskBatchUpdateFormMapper;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Mappers\UniformTaskBatchUpdateFormMapper;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskDiverseBatchSchema;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskStepSchema;
use Dpb\Modules\Tasks\TaskBatches\Enums\TaskSubjectType;
use Dpb\Modules\Tasks\TaskBatches\Models\TaskBatch;
use Dpb\Modules\Tasks\TaskBatches\Workflows\UpdateTaskBatchWorkflow;
use Filament\Actions\Action;
use Filament\Schemas\Components\Wizard\Step;

class EditTaskBatchAction
{
    public static function make(?string $name = 'edit_action'): Action
    {
        return Action::make($name)
            ->model(TaskBatch::class)
            ->schema(TaskDiverseBatchSchema::make())
            // ->steps([
            //     Step::make(__('dpb-mod-tasks::task-batch.form.steps.tasks'))
            //         ->schema(TaskStepSchema::make()),
            //     // Step::make(__('dpb-mod-tasks::task-batch.form.steps.work'))
            //     //     ->schema(WorkStepSchema::make())


            // ])
            ->fillForm(function (
                TaskBatch $record,
                DiverseTaskBatchUpdateFormMapper $mapper,
            ): array {
                $prefilled = $mapper->fromDatabase($record);
                // dd($prefilled);
                return $mapper->fromDatabase($record);
            })
            ->action(function (
                array $data,
                TaskBatch $record,
                DiverseTaskBatchUpdateFormMapper $mapper,
                UpdateTaskBatchWorkflow $wf,
            ): void {
                $command = $mapper->fromForm($data, $record);

                $wf->execute($command);
            })
            ->after(fn($record) => $record->refresh());
    }
}
