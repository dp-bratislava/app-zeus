<?php

namespace App\Filament\Resources\Fleet\MaintenanceGroupResource\Pages;

use App\Filament\Resources\Fleet\MaintenanceGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListMaintenanceGroups extends ListRecords
{
    protected static string $resource = MaintenanceGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    public function getTitle(): string | Htmlable
    {
        return '';
    }      
}
