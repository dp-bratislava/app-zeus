<?php

namespace App\Filament\Resources\TS\TicketResource\Forms;

use App\Filament\Components\VehiclePicker;
use Dpb\Package\Fleet\Models\MaintenanceGroup;
use Dpb\Package\Fleet\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;

class TicketAssignmentForm
{
    public static function make(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('tickets/ticket.form.sections.ticket'))
                    ->columns(7)
                    ->schema([
                        // date
                        Forms\Components\DatePicker::make('ticket.date')
                            ->label(__('tickets/ticket.form.fields.date'))
                            ->columnSpan(1)
                            ->default(now()),
                        // subject
                        VehiclePicker::make('subject_id')
                            ->label(__('tickets/ticket.form.fields.subject'))
                            ->columnSpan(1)
                            ->options(
                                Vehicle::with(['codes', 'model'])
                                    ->when(!auth()->user()->hasRole('super-admin'), function ($q) {
                                        $userHandledVehicleTypes = auth()->user()->vehicleTypes();
                                        $q->byType($userHandledVehicleTypes);
                                    })
                                    ->get()
                                    ->mapWithKeys(function ($vehicle) {
                                        return [
                                            $vehicle->id => $vehicle->code?->code . ' - ' . $vehicle->model?->title
                                        ];
                                    })
                            )
                            ->getOptionLabelFromRecordUsing(null)
                            ->getSearchResultsUsing(null)
                            ->preload()
                            ->searchable()
                            // ->disabled(fn($record) => $record->source_id == TicketSource::byCode('planned-maintenance')->first()->id)
                            ->required()
                            ->afterStateUpdated(
                                fn(Set $set, $state) =>
                                dd($state)
                                // $set(
                                //     'assigned_to_id',
                                //     Vehicle::with('maintenanceGroup')
                                //         ->findSole($state)
                                //         ->maintenance_group_id ?? null
                                // )
                            ),
                        // assigned to e.g. maintenance group
                        Forms\Components\ToggleButtons::make('assigned_to_id')
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
                            // ->live()
                            // ->extraAttributes([
                            //     'x-data' => '{}',
                            //     'x-on:input.debounce.500' => 'console.log($event.target.value)',
                            // ])
                            // ->default(fn (Get $get) => Vehicle::with('maintenanceGroup')->findSole($get('subject_id'))->maintenance_group_id ?? null)
                            ->inline(),
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
                    ])
            ]);
    }
}
