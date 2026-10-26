<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Actions;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Mappers\UniformTaskBatchCreateFormMapper;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskUniformBatchSchema;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\TaskBatchResource;
use Dpb\Modules\Tasks\TaskBatches\Models\TaskBatch;
use Dpb\Modules\Tasks\TaskBatches\Workflows\CreateTaskBatchWorkflow;
use Filament\Actions\Action;

class CreateUniformTaskBatchAction
{
    public static function make(?string $name = 'create_action'): Action
    {
        return Action::make($name)
            ->model(TaskBatch::class)
            ->schema(TaskUniformBatchSchema::make())
            ->action(function (
                array $data,
                UniformTaskBatchCreateFormMapper $mapper,
                CreateTaskBatchWorkflow $workflow,
            ) {
                $command = $mapper->fromForm($data);
                $result = $workflow->execute($command);
                // $taskItemIdsData = Base64UrlHelper::encode(json_encode($result->taskItemIds));
                // redirect()->route(DailyMaintenanceWorkOrdersPage::getRouteName(), [
                //     'taskItems' => $taskItemIdsData,
                // ]);
            })            
            ->url(TaskBatchResource::getUrl('create-uniform'));
    }
}
