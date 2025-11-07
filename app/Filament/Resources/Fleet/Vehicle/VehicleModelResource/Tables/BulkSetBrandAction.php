<?php

namespace App\Filament\Resources\Fleet\Vehicle\VehicleModelResource\Tables;

use App\Filament\Resources\Fleet\Vehicle\BrandResource\Froms\BrandPicker;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

class BulkSetBrandAction
{
    public static function make($uri): BulkAction
    {
        return BulkAction::make($uri)
            ->label(__('fleet/vehicle-model.table.actions.bulk_set_brand'))
            ->form([
                BrandPicker::make('brand_id')
                    ->relationship('brand', 'title')
            ])
            ->action(function (array $data, Collection $records) {
                // dd($records);
                foreach ($records as $record) {
                    $record->brand_id = $data['brand_id'];
                    $record->save();
                }
            });
    }
}
