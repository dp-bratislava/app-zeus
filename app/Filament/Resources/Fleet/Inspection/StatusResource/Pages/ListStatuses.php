<?php

namespace App\Filament\Resources\Fleet\Inspection\StatusResource\Pages;

use App\Filament\Resources\Fleet\Inspection\StatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStatuses extends ListRecords
{
    protected static string $resource = StatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
