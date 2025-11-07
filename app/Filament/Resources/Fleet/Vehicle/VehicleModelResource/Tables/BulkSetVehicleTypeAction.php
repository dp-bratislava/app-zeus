<?php

namespace App\Filament\Resources\Fleet\Vehicle\VehicleModelResource\Tables;

use App\Filament\Resources\Fleet\Vehicle\VehicleTypeResource\Forms\VehicleTypePicker;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

class BulkSetVehicleTypeAction
{
    public static function make($uri): BulkAction
    {
        return BulkAction::make($uri)
            ->label(__('fleet/vehicle-model.table.actions.bulk_set_vehicle_type'))
            ->form([
                VehicleTypePicker::make('type_id')
                    ->relationship('type', 'title')
            ])
            ->action(function (array $data, Collection $records) {
                // dd($records);
                foreach ($records as $record) {
                    $record->type_id = $data['type_id'];
                    $record->save();
                }
            });
    }
}
