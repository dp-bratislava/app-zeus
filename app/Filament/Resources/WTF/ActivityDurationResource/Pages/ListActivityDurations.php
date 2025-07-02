<?php

namespace App\Filament\Resources\WTF\ActivityDurationResource\Pages;

use App\Filament\Resources\WTF\ActivityDurationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListActivityDurations extends ListRecords
{
    protected static string $resource = ActivityDurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
