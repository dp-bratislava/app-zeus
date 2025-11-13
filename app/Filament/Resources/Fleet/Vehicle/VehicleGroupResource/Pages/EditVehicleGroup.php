<?php

namespace App\Filament\Resources\Fleet\Vehicle\VehicleGroupResource\Pages;

use App\Filament\Resources\Fleet\Vehicle\VehicleGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditVehicleGroup extends EditRecord
{
    protected static string $resource = VehicleGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function getTitle(): string | Htmlable
    {
        return __('fleet/vehicle-group.form.update_heading', ['title' => $this->record->title]);
    }      
}
