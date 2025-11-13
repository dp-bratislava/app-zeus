<?php

namespace App\Filament\Resources\Fleet\MaintenanceGroupResource\Forms;

use App\Filament\Resources\Fleet\Vehicle\BrandResource\Forms\BrandForm;
use App\Models\Datahub\EmployeeContract as Contract;
use Closure;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms;

/**
 * Extends Filament Select component.
 * 
 * Presets label and filtering. If needed it can be overriden
 * and used like original Select component.
 * Both custom methods have to be called with null as input parameter
 * to apply custom bevaiour.  
 */
class MaintenanceGroupPicker
{
    public static function make(string $uri)
    {
        return Forms\Components\Select::make($uri)
            ->label(__('fleet/maintenance-group.components.picker.label'))
            ->searchable()
            ->preload()
            ->createOptionForm(BrandForm::schema())
            ->createOptionModalHeading(__('fleet/maintenance-group.components.picker.create_heading'))
            ->editOptionForm(BrandForm::schema())
            ->editOptionModalHeading(__('fleet/maintenance-group.components.picker.update_heading'));
    }
}
