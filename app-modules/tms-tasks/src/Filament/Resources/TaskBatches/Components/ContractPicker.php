<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components;

use Carbon\CarbonImmutable;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas\TaskBatchForm;
use Dpb\Modules\Tasks\TaskBatches\Services\TaskBatchLookupScopeService;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;

class ContractPicker
{
    public static function make(
        string $name
    ): Component {
        return ToggleButtons::make($name)
            ->columnSpanFull()
            ->extraAttributes([
                'class' => 'grid grid-cols-8 gap-2 w-full [&>div>label]:w-full [&>div>label>span]:w-full',
            ])
            ->inline()
            ->options(
                fn(TaskBatchLookupScopeService $lookupSvc, Get $get) => $lookupSvc
                    ->employeeContracts(
                        CarbonImmutable::parse($get(TaskBatchForm::DATE_KEY))
                    )
                    ->mapWithKeys(function ($contract) {
                        return [
                            $contract->contract_id =>  $contract->pid . ' - ' . $contract->last_name
                        ];
                    })
                    ->toArray()
            )
            ->multiple()
            ->live();
    }
}
