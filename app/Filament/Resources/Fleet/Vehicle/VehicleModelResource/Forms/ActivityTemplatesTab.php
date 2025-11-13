<?php

namespace App\Filament\Resources\Fleet\Vehicle\VehicleModelResource\Forms;

use App\Filament\Resources\Fleet\Vehicle\BrandResource\Forms\BrandPicker;
use App\Filament\Resources\Fleet\Vehicle\VehicleTypeResource\Forms\VehicleTypePicker;
use Dpb\Package\Activities\Models\ActivityTemplate;
use Filament\Forms;
use Filament\Forms\Form;

class ActivityTemplatesTab
{
    public static function make(): array
    {
        return [

            Forms\Components\Repeater::make('activity_templates')
                ->hiddenLabel()
                ->simple(
                    Forms\Components\Select::make('template')
                    ->options(fn () => ActivityTemplate::pluck('title', 'id'))
                    ->searchable()
                    // Forms\Components\Select::make('template')
                    // ->options()
                    // ->searchable()                    
                )
                ->addable()
                ->deletable()
                ->itemNumbers()
                ->orderable(false)
                
                // ->label(__('fleet/vehicle-model.form.fields..label'))
                // ->numeric(),
        ];
    }
}
