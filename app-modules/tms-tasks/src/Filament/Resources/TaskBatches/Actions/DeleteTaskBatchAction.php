<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Actions;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\TaskBatchResource;
use Dpb\Modules\Tasks\Workflows\DeleteTaskBatchWorkflow;
use Filament\Actions\Action;
use Filament\Support\Enums\Width;

class DeleteTaskBatchAction
{
    public static function make(?string $name = 'delete_action'): Action
    {
        return Action::make($name)
            ->modalWidth(Width::FiveExtraLarge)
            ->modalHeading(__('dpb-mod-tasks::task-batch.modals.delete_action.heading'))
            ->modalDescription(__('dpb-mod-tasks::task-batch.modals.delete_action.description'))
            ->icon('heroicon-o-trash')
            ->action(function ($record, DeleteTaskBatchWorkflow $workflow) {
                $workflow->handle($record);

                return redirect(TaskBatchResource::getUrl('index'));
            })
            ->requiresConfirmation();
    }
}
