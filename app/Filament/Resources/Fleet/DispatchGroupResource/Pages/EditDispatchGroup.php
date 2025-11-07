<?php

namespace App\Filament\Resources\Fleet\DispatchGroupResource\Pages;

use App\Filament\Resources\Fleet\DispatchGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDispatchGroup extends EditRecord
{
    protected static string $resource = DispatchGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
