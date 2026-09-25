<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Actions;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Mappers\TaskBatchCreateFormMapper;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Dpb\Modules\Tasks\Models\TaskBatch;
use Dpb\Modules\Tasks\Workflows\CreateTaskBatchWorkflow;
use Filament\Actions\Action;
use Filament\Schemas\Schema;

class CreateTaskBatchAction
{
    public static function make(?string $name = 'create_action'): Action
    {
        return Action::make($name)
            ->model(TaskBatch::class)
            ->schema(fn(Schema $schema): Schema => TaskBatchForm::configure($schema))
            ->action(function (
                array $data,
                TaskBatchCreateFormMapper $mapper,
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
