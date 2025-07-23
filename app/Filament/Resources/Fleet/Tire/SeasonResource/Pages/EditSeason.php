<?php

namespace App\Filament\Resources\Fleet\Tire\SeasonResource\Pages;

use App\Filament\Resources\Fleet\Tire\SeasonResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSeason extends EditRecord
{
    protected static string $resource = SeasonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
