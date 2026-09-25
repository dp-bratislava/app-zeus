<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Actions;

use Dpb\Modules\Tasks\Enums\TaskSubjectType;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Mappers\TaskBatchUpdateFormMapper;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskStepSchema;
use Dpb\Modules\Tasks\Models\TaskBatch;
use Dpb\Modules\Tasks\Workflows\UpdateTaskBatchWorkflow;
use Filament\Actions\Action;
use Filament\Schemas\Components\Wizard\Step;

class EditTaskBatchAction
{
    public static function make(?string $name = 'edit_action'): Action
    {
        return Action::make($name)
            ->modalWidth(width: 'full')
            ->modalHeading(__('dpb-mod-tasks::task-batch.update_heading'))
            ->model(TaskBatch::class)
            // ->schema(fn(Schema $schema): Schema => TaskBatchForm::configure($schema))
            ->steps([
                Step::make(__('dpb-mod-tasks::task-batch.form.steps.tasks'))
                    ->schema(TaskStepSchema::make()),
                // Step::make(__('dpb-mod-tasks::task-batch.form.steps.work'))
                //     ->schema(WorkStepSchema::make())


            ])
            ->fillForm(function (TaskBatch $record): array {
                $prefilled = [
                    // date
                    'date' => $record->date,

                    // task group
                    TaskBatchForm::COL_NAME_TG_PICKER =>
                    $record->taskGroup->id,

                    // task item groups
                    TaskBatchForm::COL_NAME_TIG_PICKER =>
                    $record->itemGroups
                        ->pluck('task_item_group_id')
                        ->map(static fn($id) => (string) $id)
                        ->all(),

                    // vehicles
                    TaskBatchForm::COL_NAME_VEHICLE_PICKER =>
                    $record->subjects
                        ->where('subject_type', TaskSubjectType::Vehicle->value)
                        ->pluck('subject_id')
                        ->map(static fn($id) => (string) $id)
                        ->all(),
                ];

                // dd($prefilled);
                return $prefilled;
            })
            ->action(function (
                array $data,
                TaskBatch $record,
                TaskBatchUpdateFormMapper $mapper,
                UpdateTaskBatchWorkflow $wf,
            ): void {
                $command = $mapper->fromForm($data, $record);

                $wf->execute($command);
            })
            ->after(fn ($record) => $record->refresh());
    }
}
