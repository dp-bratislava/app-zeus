<?php

namespace App\Filament\Resources\Fleet\Vehicle\BrandResource\Pages;

use App\Filament\Resources\Fleet\Vehicle\BrandResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBrand extends EditRecord
{
    protected static string $resource = BrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
