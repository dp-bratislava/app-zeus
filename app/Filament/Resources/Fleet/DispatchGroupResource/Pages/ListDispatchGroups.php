<?php

namespace App\Filament\Resources\Fleet\DispatchGroupResource\Pages;

use App\Filament\Resources\Fleet\DispatchGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDispatchGroups extends ListRecords
{
    protected static string $resource = DispatchGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
