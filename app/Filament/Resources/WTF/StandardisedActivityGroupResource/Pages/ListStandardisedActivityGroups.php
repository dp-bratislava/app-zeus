<?php

namespace App\Filament\Resources\WTF\StandardisedActivityGroupResource\Pages;

use App\Filament\Resources\WTF\StandardisedActivityGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStandardisedActivityGroups extends ListRecords
{
    protected static string $resource = StandardisedActivityGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
