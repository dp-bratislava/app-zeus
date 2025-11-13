<?php

namespace App\Filament\Resources\Inspection\InspectionResource\Forms;

use App\Filament\Components\VehiclePicker;
use App\Filament\Resources\Inspection\InspectionTemplateResource\Forms\InspectionTemplatePicker;
use Carbon\Carbon;
use Dpb\Package\Fleet\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Form;

class InspectionFrom
{
    public static function make(Form $form): Form
    {
        return $form->schema(static::schema());
    }


    public static function schema(): array
    {
        return [
            // date
            Forms\Components\DatePicker::make('date')
                ->label(__('inspections/inspection.form.fields.date'))
                ->default(Carbon::now()),
            // template
            InspectionTemplatePicker::make('template_id')
                ->label(__('inspections/inspection.form.fields.template'))
                ->relationship('template', 'title'),
            // subject
                Forms\Components\Select::make('subject_id')
                    ->label(__('inspections/inspection.form.fields.subject'))
                    ->options(Vehicle::with('model')->get()
                        ->mapWithKeys(fn(Vehicle $vehicle) => [$vehicle->id => $vehicle->code->code . ' - ' . $vehicle->model?->title])
                    )
                    ->searchable(),

                // ->relationship('template', 'title'),
            // Forms\Components\TextInput::make('description')
            //     ->columnSpan(1)
            //     ->label(__('fleet/maintenance-group.form.fields.description')),
            // Forms\Components\ColorPicker::make('color')
            //     ->columnSpan(1)
            //     ->label(__('fleet/maintenance-group.form.fields.color')),
        ];
    }
}
