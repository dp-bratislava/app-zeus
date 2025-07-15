<?php

namespace App\Filament\Resources\TS\StandardisedActivityGroupResource\Pages;

use App\Filament\Resources\TS\StandardisedActivityGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStandardisedActivityGroup extends EditRecord
{
    protected static string $resource = StandardisedActivityGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
