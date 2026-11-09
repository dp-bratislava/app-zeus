<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Actions;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\TaskBatchResource;
use Dpb\Modules\Tasks\TaskBatches\Models\TaskBatch;
use Filament\Actions\Action;

class BackToViewAction
{
    public static function make(
        TaskBatch $record,
        ?string $name = 'back_to_view_action'
    ): Action {
        return Action::make($name)
            ->url(TaskBatchResource::getUrl('view', [$record]))
            ->label(__('dpb-mod-tasks::task-batch.form.actions.back_to_view_action'))
            ->icon('heroicon-o-arrow-left');
    }
}
