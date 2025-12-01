<?php

namespace App\Filament\Resources\Fleet\Vehicle\BrandResource\Pages;

use App\Filament\Resources\Fleet\Vehicle\BrandResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditBrand extends EditRecord
{
    protected static string $resource = BrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function getTitle(): string | Htmlable
    {
        return __('fleet/vehicle-brand.update_heading', ['title' => $this->record->title]);
    }      
}
