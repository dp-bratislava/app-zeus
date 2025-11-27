<?php

namespace App\Filament\Resources\Inspection\InspectionTemplateGroupResource\Forms;

use App\Filament\Components\VehiclePicker;
use App\Filament\Resources\Inspection\InspectionTemplateResource\Forms\InspectionTemplatePicker;
use Carbon\Carbon;
use Dpb\Package\Fleet\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Form;

class InspectionTempalteGroupFrom
{
    public static function make(Form $form): Form
    {
        return $form->schema(static::schema());
    }


    public static function schema(): array
    {
        return [
            // code
            Forms\Components\TextInput::make('code')
                ->label(__('inspections/inspection-template-group.form.fields.code.label'))
                ->hint(__('inspections/inspection-template-group.form.fields.code.hint')),
            // title
            Forms\Components\TextInput::make('title')
                ->columnSpan(1)
                ->label(__('inspections/inspection-template-group.form.fields.title')),
            // description
            Forms\Components\TextInput::make('description')
                ->columnSpan(1)
                ->label(__('inspections/inspection-template-group.form.fields.description')),
            // template
            // InspectionTemplatePicker::make('template_id')
            //     ->label(__('inspections/inspection.form.fields.template'))
            //     ->relationship('template', 'title'),
            // // subject
            //     Forms\Components\Select::make('subject_id')
            //         ->label(__('inspections/inspection.form.fields.subject'))
            //         ->options(Vehicle::with('model')->get()
            //             ->mapWithKeys(fn(Vehicle $vehicle) => [$vehicle->id => $vehicle->code->code . ' - ' . $vehicle->model?->title])
            //         )
            //         ->searchable(),

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
