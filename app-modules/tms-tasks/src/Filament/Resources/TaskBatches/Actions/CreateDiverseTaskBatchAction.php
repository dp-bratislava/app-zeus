<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Actions;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\TaskBatchResource;
use Filament\Actions\Action;

class CreateDiverseTaskBatchAction
{
    public static function make(?string $name = 'create_diverse_action'): Action
    {
        return Action::make($name)
            ->url(TaskBatchResource::getUrl('create-diverse'));
    }
}
