<?php

namespace App\Filament\Resources\WTF\ActivityDurationResource\Pages;

use App\Filament\Resources\WTF\ActivityDurationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditActivityDuration extends EditRecord
{
    protected static string $resource = ActivityDurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
