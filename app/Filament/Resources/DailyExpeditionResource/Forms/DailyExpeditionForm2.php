<?php

namespace App\Filament\Resources\DailyExpeditionResource\Forms;

use Carbon\Carbon;
use Dpb\Package\Fleet\Models\MaintenanceGroup;
use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Package\Fleet\Models\VehicleModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;

class DailyExpeditionForm2
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
                ->description('TO DO: pripravujeme. Náhľad č. 2')
                ->schema([

                    // maintenance group
                    self::maintenanceGroupField(),
                    // vehicles
                    self::vehiclesField(),
                ])
        ];
    }

    private static function maintenanceGroupField()
    {
        return Forms\Components\ToggleButtons::make('maintenance_group_id')
            ->label(__('tickets/ticket.form.fields.assigned_to'))
            ->columnSpan(2)
            ->options(
                fn() =>
                MaintenanceGroup::when(!auth()->user()->hasRole('super-admin'), function ($q) {
                    $userHandledVehicleTypes = auth()->user()->vehicleTypes();
                    $q->byVehicleType($userHandledVehicleTypes);
                })
                    ->pluck('code', 'id')
            )
            ->dehydrated()
            ->live()
            ->inline();
    }

    private static function vehiclesField()
    {
        $models = VehicleModel::select(['id', 'title'])
            ->with('vehicles:id,maintenance_group_id,code_1,model_id')
            ->byType(['A', 'E', 'T'])
            ->get();

        foreach ($models as $model) {
            $sections[] = Forms\Components\Section::make($model->title)
                // ->description(function (Get $get) use ($model) {
                //     $mc = $get('assigned_to_id');
                //     // return (string)
                //     dd(
                //         $model->select(['id', 'title'])
                //             ->whereHas('vehicles', function ($q) use ($mc) {
                //                 $q->whereNotNull('code_1')
                //                     ->where('maintenance_group_id', '=', $mc);
                //             })
                //             ->get()
                //     );
                // })
                ->columnSpan(1)
                // ->hidden(fn (Get $get) => ! $get('maintenance_group_id'))
                ->hidden(function (Get $get) use ($model) {
                    $mc = $get('maintenance_group_id');
                    $model->whereHas('vehicles', function ($q) use ($mc) {
                        $q->whereNotNull('code_1')
                            ->where('maintenance_group_id', '=', $mc);
                    })
                        ->get()->count() == 0;
                })
                ->schema([
                    Forms\Components\CheckboxList::make('vehicles')
                        ->hiddenLabel()
                        ->options(
                            function (Get $get) use ($model) {
                                return $model->vehicles
                                    ->whereNotNull('code_1')
                                    ->where('maintenance_group_id', '=', $get('maintenance_group_id'))
                                    ->pluck('code_1', 'id');
                            }
                        )
                        // ->searchable()
                        ->bulkToggleable()
                        ->columns(4)
                        ->columnSpan(2),
                ]);
        }
        return Forms\Components\Section::make($sections)
            ->columns(4);
        // return $sections;

        /*
        return Forms\Components\Section::make([
            Forms\Components\CheckboxList::make('vehicles')
                ->label(__('inspections/daily-maintenance.form.fields.vehicles'))
                ->options(function (Get $get) {
                    $mgId = $get('assigned_to_id');
                    $mgCode = null;

                    if ($mgId !== null) {
                        $mgCode = MaintenanceGroup::findSole($get('assigned_to_id'))?->code; //'1TPA';
                    }

                    // dd($mgCode);
                    if ($mgCode !== null) {
                        return Vehicle::whereNotNull('code_1')
                            ->with('model:id,title')
                            ->byMaintenanceGroup($mgCode)
                            ->pluck('code_1', 'id');
                    }
                    return [];
                })
                ->searchable()
                ->bulkToggleable()
                ->columns(4)
                ->columnSpan(2),
        ]);
        */
    }
}
