<?php

namespace App\Filament\Resources\Inspection\DailyMaintenanceResource\Pages;

use App\Filament\Resources\Inspection\DailyMaintenanceResource;
use App\Services\Inspection\DailyMaintenanceService;
use App\Services\TS\TicketAssignmentService;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\MaxWidth;

class ListDailyMaintenances extends ListRecords
{
    protected static string $resource = DailyMaintenanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                // ->using(function(array $data, DailyMaintenanceService $dmSvc) {
                //     $dmSvc->create($data);
                //     // logger()->info('form data before save: ', $data);
                //     // return $data;
                // })
                ->using(function(array $data, TicketAssignmentService $taSvc) {
                    $taSvc->createFromDailyMaintenance($data);
                })
                ->modalWidth(MaxWidth::class),
        ];
    }
}
