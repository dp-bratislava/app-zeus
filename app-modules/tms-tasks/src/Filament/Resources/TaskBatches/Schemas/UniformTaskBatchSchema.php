<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas;

use Filament\Schemas\Components\Tabs;

class UniformTaskBatchSchema
{
    public static function make(): array
    {
        return [
            Tabs::make('Tabs')
                ->tabs([
                    TaskWorkTab::make(),
                    TaskSubjectTab::make()
                ])
        ];
    }
}
