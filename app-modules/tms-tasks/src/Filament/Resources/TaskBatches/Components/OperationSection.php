<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Dpb\Modules\Tasks\TaskBatches\Services\TaskBatchLookupScopeService;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;

class OperationSection
{
    public static function make(
        ?string $name = TaskBatchForm::COL_NAME_CONTRACT_PICKER
    ): Component {
        return Section::make('operations')
            ->schema([
                TaskGroupPicker::make(),
                TaskItemGroupPicker::make(),
                OperationPicker::make(),
            ]);
    }
}
