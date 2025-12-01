<?php

namespace App\Filament\Resources\Fleet\Vehicle\VehicleTypeResource\Pages;

use App\Filament\Resources\Fleet\Vehicle\VehicleTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditVehicleType extends EditRecord
{
    protected static string $resource = VehicleTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function getTitle(): string | Htmlable
    {
        return __('fleet/vehicle-type.update_heading', ['title' => $this->record->title]);
    }     
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // vehicles
        $data['models'] = $this->record->models?->pluck('id');

        return $data;
    }    
}
