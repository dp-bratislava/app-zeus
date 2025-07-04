<?php

namespace App\Filament\Resources\WTF\PlannedActivityResource\Pages;

use App\Filament\Resources\WTF\PlannedActivityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlannedActivities extends ListRecords
{
    protected static string $resource = PlannedActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
