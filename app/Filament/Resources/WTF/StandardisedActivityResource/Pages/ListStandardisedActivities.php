<?php

namespace App\Filament\Resources\WTF\StandardisedActivityResource\Pages;

use App\Filament\Resources\WTF\StandardisedActivityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStandardisedActivities extends ListRecords
{
    protected static string $resource = StandardisedActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
