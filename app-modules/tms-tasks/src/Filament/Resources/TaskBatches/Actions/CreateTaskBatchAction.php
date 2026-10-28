<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Actions;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Mappers\UniformTaskBatchCreateFormMapper;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Dpb\Modules\Tasks\TaskBatches\Models\TaskBatch;
use Dpb\Modules\Tasks\TaskBatches\Workflows\CreateTaskBatchWorkflow;
use Filament\Actions\Action;
use Filament\Schemas\Schema;

class CreateTaskBatchAction
{
    public static function make(?string $name = 'create_action'): Action
    {
        return Action::make($name)
            ->model(TaskBatch::class)
            ->schema(fn(Schema $schema): Schema => TaskBatchForm::configure($schema, TaskBatchForm::IS_UNIFORM))
            ->action(function (
                array $data,
                UniformTaskBatchCreateFormMapper $mapper,
                CreateTaskBatchWorkflow $workflow,
            ) {
                // dd($data);
// dd([
//     'selected_groups' => $data[TaskBatchForm::COL_NAME_TIG_PICKER] ?? null,
//     'operations_by_group' => $data[TaskBatchForm::COL_NAME_OPERATION_PICKER] ?? null,
//     'test_operations' => $data['test_operations'] ?? null,
// ]);

                $command = $mapper->fromForm($data);
                // dd($command);
                $result = $workflow->execute($command);
                // $taskItemIdsData = Base64UrlHelper::encode(json_encode($result->taskItemIds));
                // redirect()->route(DailyMaintenanceWorkOrdersPage::getRouteName(), [
                //     'taskItems' => $taskItemIdsData,
                // ]);
            })            
            ->modalWidth(width: 'full')
            ->modalHeading(__('dpb-mod-tasks::task-batch.create_heading'));
    }
}
