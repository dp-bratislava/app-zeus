<?php

namespace App\Filament\Resources\WTF\ActivityTypeResource\Pages;

use App\Filament\Resources\WTF\ActivityTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditActivityType extends EditRecord
{
    protected static string $resource = ActivityTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
