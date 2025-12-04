<?php

namespace App\Filament\Resources\DailyExpeditionResource\Forms;

use Carbon\Carbon;
use Dpb\Package\Fleet\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Form;

class DailyExpeditionForm
{
    public static function defaultVehicles(): array
    {
        return Vehicle::whereHas(
            'model',
            fn($q) =>
            $q->whereLike('title', 'SOR%')
        )
            ->limit(10)
            ->get()
            ->map(fn($vehicle) => [
                'vehicle_id' => $vehicle->id,
                'vehicle_title' => $vehicle->code->code, // . ' - ' . $vehicle?->model?->title,
                'state' => 'ok',
                'service' => null,
                'note' => null,
            ])
            ->toArray();
    }

    public static function make(Form $form): Form
    {
        return $form
            ->schema(static::schema())
            ->columns(6);
    }

    public static function schema(): array
    {
        return [
            Forms\Components\Section::make('TO DO')
                ->description('TO DO: pripravujeme. Náhľad č. 1')
                ->schema([
                    // date
                    Forms\Components\DatePicker::make('date')
                        ->label(__('daily-expedition.form.fields.date'))
                        ->default(Carbon::now()),
                    // vehicles
                    // VehicleRepeater::make('vehicles', $vehicles),
                    VehicleRepeater::make('vehicles')
                        ->label(__('daily-expedition.form.fields.vehicles_repeater.label')),
                ])
        ];
    }
}
