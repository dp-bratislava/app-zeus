<?php

namespace App\Filament\Resources\TS\TicketSourceResource\Pages;

use App\Filament\Resources\TS\TicketSourceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTicketSource extends EditRecord
{
    protected static string $resource = TicketSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
