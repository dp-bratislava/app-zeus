<?php

namespace App\Filament\Resources\Fleet\Vehicle\VehicleModelResource\Pages;

use App\Filament\Resources\Fleet\Vehicle\VehicleModelResource;
use Dpb\Package\Fleet\Models\Vehicle;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditVehicleModel extends EditRecord
{
    protected static string $resource = VehicleModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    public function getTitle(): string | Htmlable
    {
        return __('fleet/vehicle-model.update_heading', ['title' => $this->record->title]);
    }      

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // vehicles
        $data['vehicles'] =$this->record->vehicles->pluck('id');

        return $data;
    }

}
