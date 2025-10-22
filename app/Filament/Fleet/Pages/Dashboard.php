<?php

namespace App\Filament\Fleet\Pages;

use App\Filament\Fleet\Widgets\Vehicle\VehiclesByMaintenanceGroup;
use App\Filament\Fleet\Widgets\Vehicle\VehiclesByModel;
use App\Filament\Fleet\Widgets\Vehicle\VehiclesByState;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    // protected static string $routePath = 'finance';
    protected static ?string $title = 'Fleet dashboard';

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        DatePicker::make('startDate'),
                        DatePicker::make('endDate'),
                        // ...
                    ])
                    ->columns(3),
            ]);
    }

    public function getWidgets(): array
    {
        return [
            VehiclesByState::class,
            VehiclesByModel::class,
            VehiclesByMaintenanceGroup::class,
        ];
    }    
}
