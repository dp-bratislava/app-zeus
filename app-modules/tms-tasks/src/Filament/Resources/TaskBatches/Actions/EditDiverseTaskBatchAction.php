<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Actions;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\TaskBatchResource;
use Dpb\Modules\Tasks\TaskBatches\Models\TaskBatch;
use Filament\Actions\Action;

class EditDiverseTaskBatchAction
{
    public static function make(?string $name = 'edit_diverse_action'): Action
    {
        return Action::make('editUniform')
            ->icon('heroicon-o-pencil-square')
            ->url(
                fn(TaskBatch $record): string =>
                TaskBatchResource::getUrl('edit-diverse', [
                    'record' => $record,
                ])
            );
    }
}
