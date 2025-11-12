<?php

namespace App\Filament\Resources\TS\TicketResource\Forms;

use App\Filament\Components\DepartmentPicker;
use App\Filament\Resources\Fleet\Vehicle\VehicleResource\Forms\VehiclePicker;
use App\Filament\Resources\TS\TicketResource\Components\ActivityRepeater;
use App\Filament\Resources\TS\TicketResource\Components\MaterialRepeater;
use App\Filament\Resources\TS\TicketResource\Components\ServiceRepeater;
use App\Services\Activity\Activity\WorkService;
use App\Services\TS\ActivityService;
use App\Services\TS\HeaderService;
use App\Services\TS\SubjectService;
use Dpb\Package\Fleet\Models\MaintenanceGroup;
use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Package\Fleet\Models\VehicleType;
use Dpb\Package\Tickets\Models\Ticket;
use Dpb\Package\Tickets\Models\TicketSource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;

class TicketAssignmentForm
{
    public static function make(Form $form): Form
    {
        return $form
            ->columns(7)
            ->schema([
                Forms\Components\DatePicker::make('ticket.date')
                    ->label(__('tickets/ticket.form.fields.date'))
                    ->columnSpan(1)
                    ->default(now()),
                // VehiclePicker::make('subject')
                //     ->label(__('tickets/ticket.form.fields.subject'))
                //     ->columnSpan(1)
                //     // ->relationship('department', 'title')
                //     ->getOptionLabelFromRecordUsing(null)
                //     ->getSearchResultsUsing(null)
                //     ->searchable(),
                // subject
                Forms\Components\Select::make('subject_id')
                    ->label(__('tickets/ticket.form.fields.subject'))
                    ->columnSpan(1)
                    // ->relationship('source', 'title', null, true)
                    ->options(
                        function (Get $get) {
                            // $vehicleType = 'A';
                            $vehicleType = ($get('assigned_to_id') != null) ? MaintenanceGroup::findSole($get('assigned_to_id'))?->vehicleType?->code : null;
                            // $vehicleType = VehicleType::findSole($get('assigned_to'))?->code;
                            // print_r($get('assigned_to'));

                            return Vehicle::when($vehicleType != null, function ($qeury, $vehicleType) {
                                $qeury->byType($vehicleType);
                            })
                            ->pluck('code_1', 'id');
                                // ->get()
                                // ->mapWithKeys(function ($vehicle) {
                                //     return [
                                //         'id' => $vehicle->id,
                                //         'label' => $vehicle->code->code . ' - ' . $vehicle?->model?->title
                                //     ];
                                // });
                        }
                    )
                    // ->getOptionLabelUsing(fn(Vehicle $record) => "{$record->code->code} - {$record->model->title}")
                    ->preload()
                    ->searchable()
                    // ->disabled(fn($record) => $record->source_id == TicketSource::byCode('planned-maintenance')->first()->id)
                    ->required(false),
                // assigned to e.g. maintenance group
                Forms\Components\ToggleButtons::make('assigned_to_id')
                    ->label(__('tickets/ticket.form.fields.assigned_to'))
                    ->columnSpan(2)
                    ->options(fn() => MaintenanceGroup::pluck('code', 'id'))
                    ->live()
                        ->extraAttributes([
        'x-data' => '{}',
        'x-on:input.debounce.500' => 'console.log($event.target.value)',
    ])
                    ->inline(),
                // // assigned to
                // Forms\Components\MorphToSelect::make('assignedTo')
                //     ->types([
                //         Forms\Components\MorphToSelect\Type::make(MaintenanceGroup::class)
                //             ->titleAttribute('code'),
                //     ])
                //     // ->relationship('subject', 'code')
                //     // ->options(fn() => Vehicle::pluck('code', 'id'))
                //     ->preload()
                //     ->searchable(),

                // group
                Forms\Components\Select::make('ticket.group_id')
                    ->label(__('tickets/ticket.form.fields.group'))
                    ->relationship('ticket.group', 'title')
                    ->live(),
                // source
                Forms\Components\Select::make('source')
                    ->label(__('tickets/ticket.form.fields.source'))
                    // ->relationship('source', 'title', null, true)
                    ->options([
                        'inspection' => 'Kontrola',
                        'incident' => 'Dispečing'
                    ])
                    ->searchable()
                    ->required(false),
                // description
                Forms\Components\Textarea::make('ticket.description')
                    ->label(__('tickets/ticket.form.fields.description'))
                    ->columnSpanFull(),


                // vehicle
                // Forms\Components\MorphToSelect::make('subject')
                //     ->types([
                //         MorphToSelect\Type::make(FleetVehicle::class)
                //             ->titleAttribute('code'),
                //     ])
                //     // ->relationship('subject', 'code')
                //     // ->options(fn() => Vehicle::pluck('code', 'id'))
                //     ->preload()
                //     ->searchable(),

                // states
                // Forms\Components\Select::make('state')                                        
                //     ->options(function($record) {
                //         $record->trtransitionableStateInstances();
                //     })
                //     ->preload()
                //     ->searchable(),                    

                // activities 
                // Forms\Components\Tabs::make('Tabs')
                //     ->columnSpanFull()
                //     ->tabs([
                //         // activities
                //         Forms\Components\Tabs\Tab::make('activities')
                //             ->label(__('tickets/ticket.form.tabs.activities'))
                //             ->badge(3)
                //             ->icon('heroicon-m-wrench')
                //             ->schema([
                //                 ActivityRepeater::make('activities')
                //                 // ->relationship('activities'),
                //             ]),
                //         // materials
                //         Forms\Components\Tabs\Tab::make('materials')
                //             ->label(__('tickets/ticket.form.tabs.materials'))
                //             ->icon('heroicon-m-rectangle-stack')
                //             ->badge(2)
                //             ->schema([
                //                 MaterialRepeater::make('materials')
                //                 // ->relationship('materials'),
                //             ]),
                //         // services
                //         Forms\Components\Tabs\Tab::make('services')
                //             ->label(__('tickets/ticket.form.tabs.services'))
                //             ->badge(0)
                //             ->icon('heroicon-m-user-group')
                //             ->schema([
                //                 ServiceRepeater::make('services')
                //                 // ->relationship('services'),
                //             ])
                //     ]),
            ]);
    }
}
