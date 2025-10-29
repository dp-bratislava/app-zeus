<?php

namespace App\Filament\Resources\Incident\IncidentResource\Forms;

use Carbon\Carbon;
use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Package\Incidents\Models\IncidentType;
use Filament\Forms;
use Filament\Forms\Form;

class IncidentForm
{
    public static function make(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->label(__('incidents/incident.form.fields.date'))
                    ->default(Carbon::now()),
                Forms\Components\ToggleButtons::make('type_id')
                    ->label(__('incidents/incident.form.fields.type'))
                    ->inline()
                    ->options(
                        fn() =>
                        IncidentType::pluck('title', 'id')
                    ),
                Forms\Components\Select::make('subject_id')
                    ->label(__('tickets/ticket.form.fields.subject'))
                    ->columnSpan(3)
                    // ->relationship('source', 'title', null, true)
                    ->options(fn() => Vehicle::pluck('code_1', 'id'))
                    ->preload()
                    ->searchable()
                    // ->disabled(fn($record) => $record->source_id == TicketSource::byCode('planned-maintenance')->first()->id)
                    ->required(false),   

                Forms\Components\Textarea::make('description')
                    ->label(__('incidents/incident.form.fields.description'))
                    ->rows(10)
                    ->cols(20),
            ]);

    }
}
