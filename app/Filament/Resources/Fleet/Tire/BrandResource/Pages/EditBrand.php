<?php

namespace App\Filament\Resources\Fleet\Tire\BrandResource\Pages;

use App\Filament\Resources\Fleet\Tire\BrandResource;
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
