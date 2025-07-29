<?php

namespace App\Filament\Resources\Fleet\ServiceGroupResource\Pages;

use App\Filament\Resources\Fleet\ServiceGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListServiceGroups extends ListRecords
{
    protected static string $resource = ServiceGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
