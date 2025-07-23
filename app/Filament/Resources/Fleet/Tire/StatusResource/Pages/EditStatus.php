<?php

namespace App\Filament\Resources\Fleet\Tire\StatusResource\Pages;

use App\Filament\Resources\Fleet\Tire\StatusResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStatus extends EditRecord
{
    protected static string $resource = StatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
