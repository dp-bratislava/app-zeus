<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Actions;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Mappers\TaskDiverseBatchCreateFormMapper;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskDiverseBatchSchema;
use Dpb\Modules\Tasks\TaskBatches\Models\TaskBatch;
use Dpb\Modules\Tasks\TaskBatches\Workflows\CreateTaskBatchWorkflow;
use Filament\Actions\Action;

class CreateTaskDiverseBatchAction
{
    public static function make(?string $name = 'create_action'): Action
    {
        return Action::make($name)
            ->model(TaskBatch::class)
            ->schema(TaskDiverseBatchSchema::make())
            ->action(function (
                array $data,
                TaskDiverseBatchCreateFormMapper $mapper,
                CreateTaskBatchWorkflow $workflow,
            ) {
                $command = $mapper->fromForm($data);
                $result = $workflow->handle($command);

                // $taskItemIdsData = Base64UrlHelper::encode(json_encode($result->taskItemIds));
                // redirect()->route(DailyMaintenanceWorkOrdersPage::getRouteName(), [
                //     'taskItems' => $taskItemIdsData,
                // ]);
            })            
            ->modalWidth(width: 'full')
            ->modalHeading(__('dpb-mod-tasks::task-batch.create_heading'));
    }
}
