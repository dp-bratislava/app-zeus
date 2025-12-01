<?php

namespace App\Filament\Resources\Incident\IncidentResource\Forms;

use App\Filament\Components\VehiclePicker;
use App\Filament\Resources\Incident\IncidentTypeResource\Forms\IncidentTypePicker;
use Carbon\Carbon;
use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Package\Incidents\Models\IncidentType;
use Dpb\Package\Tickets\Models\TicketGroup;
use Filament\Forms;
use Filament\Forms\Form;

class IncidentAssignmentForm
{
    public static function make(Form $form): Form
    {
        return $form
            ->schema(static::schema())
            ->columns(6);
    }

    public static function schema(): array
    {
        return [
            // date
            Forms\Components\DatePicker::make('incident.date')
                ->label(__('incidents/incident.form.fields.date'))
                ->columnSpan(1)
                ->default(Carbon::now()),
            // ticket group
            // Forms\Components\ToggleButtons::make('ticket_group_id')
            //     ->label(__('incidents/incident.form.fields.type'))
            //     ->inline()
            //     ->options(
            //         fn() =>
            //         TicketGroup::pluck('title', 'id')
            //     ),
            // subject
            // Forms\Components\Select::make('subject_id')
            //     ->label(__('tickets/ticket.form.fields.subject'))
            //     ->columnSpan(1)
            //     // ->relationship('source', 'title', null, true)
            //     ->options(
            //         fn() => Vehicle::with(['codes', 'model:id,title'])

            //             ->get()
            //             ->mapWithKeys(fn($vehicle) => [$vehicle->id => $vehicle->code->code . ' - ' . $vehicle->model?->title])
            //     )
            //     ->getSearchResultsUsing(function (string $search) {
            //         // return Vehicle::whereHas('codes', function ($q) use ($search) {
            //         //     $q->where('code', 'like', "%{$search}%");
            //         // })
            //         //     ->with(['codes' => fn($q) => $q->where('code', 'like', "%{$search}%")->orderByDesc('date_from')])
            //         //     ->get()
            //         //     ->mapWithKeys(function (Vehicle $vehicle) {
            //         //         $latestCode = $vehicle->codes->first();
            //         //         if (!$latestCode) {
            //         //             return []; // important: return empty array if no code
            //         //         }
            //         //         return [
            //         //             $vehicle->id => $latestCode->code . ' - ' . $vehicle->model->title,
            //         //         ];
            //         //     })
            //         //     ->toArray(); // must return array
            //         return                  Vehicle::whereHas('codes', function ($q) use ($search) {
            //             $q->whereLike('code', "%{$search}%")
            //                 ->orderByDesc('date_from')
            //                 ->first();
            //         })
            //             // ->orWhereLike('title', "%{$search}%")
            //             ->get()
            //             ->mapWithKeys(fn(Vehicle $vehicle) => [$vehicle->id => $vehicle->code->code . ' - ' . $vehicle->model->title]);
            //     })
            VehiclePicker::make('subject_id')
                ->label(__('incidents/incident.form.fields.subject'))
                ->columnSpan(1)
                ->options(Vehicle::with(['codes', 'model'])
                    ->get()
                    ->mapWithKeys(function($vehicle) {
                        return [
                            $vehicle->id => $vehicle->code->code . ' - ' . $vehicle->model?->title
                        ];
                    })
                )
                ->getOptionLabelFromRecordUsing(null)
                ->getSearchResultsUsing(null)
                ->preload()
                ->searchable(),
                // ->disabled(fn($record) => $record->source_id == TicketSource::byCode('planned-maintenance')->first()->id)
                // ->required(false),
            // incident type
            IncidentTypePicker::make('incident.type_id'),
            // Forms\Components\ToggleButtons::make('incident.type_id')
            //     ->label(__('incidents/incident.form.fields.type'))
            //     ->inline()
            //     ->columnSpan(1)
            //     ->options(
            //         fn() =>
            //         IncidentType::pluck('title', 'id')
            //     ),
            // description
            Forms\Components\Textarea::make('incident.description')
                ->label(__('incidents/incident.form.fields.description'))
                ->columnSpanFull()
                ->rows(10)
                ->cols(20),
        ];
    }
}
