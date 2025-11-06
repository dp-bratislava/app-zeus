<?php

namespace App\Filament\Resources\DailyExpeditionResource\Pages;

use App\Filament\Resources\DailyExpeditionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDailyExpedition extends EditRecord
{
    protected static string $resource = DailyExpeditionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
