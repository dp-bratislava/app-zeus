<?php

namespace App\Filament\Resources\TS\TicketResource\Forms;

use Dpb\Package\Fleet\Models\MaintenanceGroup;
use Dpb\Package\Fleet\Models\Vehicle;
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
                // date
                Forms\Components\DatePicker::make('ticket.date')
                    ->label(__('tickets/ticket.form.fields.date'))
                    ->columnSpan(1)
                    ->default(now()),
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
            ]);
    }
}
