<?php

namespace App\Filament\Resources\TS\Issue\StatusResource\Pages;

use App\Filament\Resources\TS\Issue\StatusResource;
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
