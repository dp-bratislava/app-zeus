<?php

namespace App\Filament\Resources\BM\CompoundResource\Pages;

use App\Filament\Resources\BM\CompoundResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompound extends EditRecord
{
    protected static string $resource = CompoundResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
