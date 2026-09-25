<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components\ContractPicker;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components\WtfOperationPicker;
use Filament\Schemas\Components\Grid;

class WorkStepSchema
{
    public static function make(): array
    {
        return [
            Grid::make()
                ->columnSpanFull()
                ->schema([
                    ContractPicker::make()
                        ->columns(3),
                    WtfOperationPicker::make()
                        ->columns(3),
                ]),
        ];
    }
}
