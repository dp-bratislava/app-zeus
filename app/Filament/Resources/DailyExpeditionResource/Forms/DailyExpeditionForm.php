<?php

namespace App\Filament\Resources\DailyExpeditionResource\Forms;

use App\Filament\Components\DepartmentPicker;
use App\Filament\Components\VehiclePicker;
use App\Filament\Resources\TS\TicketItemResource\Forms\VehicleRepeater;
use App\Models\Datahub\Department;
use App\Models\Datahub\EmployeeContract;
use App\Services\Inspection\TemplateAssignmentService;
use Carbon\Carbon;
use Dpb\Package\Activities\Models\ActivityTemplate;
use Dpb\Package\Fleet\Models\DispatchGroup;
use Dpb\Package\Fleet\Models\MaintenanceGroup;
use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Package\Inspections\Models\InspectionTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Get;
use Illuminate\Support\Collection;

class DailyExpeditionForm
{
    public static function defaultVehicles(): array
    {
        return Vehicle::whereHas('model', fn($q) => 
                $q->whereLike('title', 'SOR%')
            )
            ->limit(10)
            ->get()
            ->map(fn($vehicle) => [
                'vehicle_id' => $vehicle->id,
                'vehicle_title' => $vehicle->code->code . ' - ' . $vehicle?->model?->title,
                'state' => 'ok',
                'service' => null,
                'note' => null,
            ])
            ->toArray();
    }
    
    public static function make(Form $form): Form
    {
        // $vehicles = Vehicle::whereHas('model', function($q) {
        //     return $q->whereLike('title', 'SOR%');
        // })
        // ->limit(10)
        // ->get()
        // ->map(function($vehicle) {
        //     return [
        //         'vehicle_id' => $vehicle->id,
        //         'vehicle_title' => $vehicle->code->code . ' - ' . $vehicle?->model?->title,
        //         'state' => 'ok',
        //         'service' => null,
        //         'note' => null
        //     ];
        // });
        return $form
            ->columns(6)
            ->schema([
                // date
                Forms\Components\DatePicker::make('date')
                    ->label(__('daily-expedition.form.fields.date'))
                    ->default(Carbon::now()),
                // vehicles
                // VehicleRepeater::make('vehicles', $vehicles),
                VehicleRepeater::make('vehicles'),
            ]);
    }
}
