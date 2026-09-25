<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Actions;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\TaskBatchResource;
use Filament\Actions\Action;

class RedirectToIndexAction
{
    public static function make(?string $name = 'back_action'): Action
    {
        return Action::make($name)        
            ->url(TaskBatchResource::getUrl('index'))
            ->icon('heroicon-o-arrow-left');
    }
}
