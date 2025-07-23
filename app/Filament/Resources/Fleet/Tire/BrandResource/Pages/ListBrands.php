<?php

namespace App\Filament\Resources\Fleet\Tire\BrandResource\Pages;

use App\Filament\Resources\Fleet\Tire\BrandResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBrands extends ListRecords
{
    protected static string $resource = BrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
