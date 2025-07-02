<?php

namespace App\Filament\Resources\WTF\ActivityGroupResource\Pages;

use App\Filament\Resources\WTF\ActivityGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditActivityGroup extends EditRecord
{
    protected static string $resource = ActivityGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
