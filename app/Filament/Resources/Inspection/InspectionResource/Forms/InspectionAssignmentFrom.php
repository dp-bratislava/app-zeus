<?php

namespace App\Filament\Resources\Inspection\InspectionResource\Forms;

use App\Filament\Components\VehiclePicker;
use App\Filament\Resources\Inspection\InspectionTemplateResource\Forms\InspectionTemplatePicker;
use Carbon\Carbon;
use Dpb\Package\Fleet\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Builder;

class InspectionAssignmentFrom
{
    public static function make(Form $form): Form
    {
        return $form->schema(static::schema());
    }


    public static function schema(): array
    {
        return [
            // date
            Forms\Components\DatePicker::make('inspection.date')
                ->label(__('inspections/inspection.form.fields.date'))
                ->default(Carbon::now()),
            // template
            InspectionTemplatePicker::make('inspection.template_id')
                ->label(__('inspections/inspection.form.fields.template'))
                ->relationship('inspection.template', 'title'),
            // subject

            // Forms\Components\MorphToSelect::make('subject')
            //     ->types([
            //         Forms\Components\MorphToSelect\Type::make(Vehicle::class)
            //             ->titleAttribute('vin')
            //             ->modifyOptionsQueryUsing(
            //                 fn(Builder $query) => $query
            //                     ->with(['model'])                                
            //                     // ->mapWithKeys(fn(Vehicle $vehicle) => [
            //                     //     $vehicle->id => $vehicle->code->code . ' - ' . $vehicle->model?->title
            //                     // ])
            //             ),
            //     ])

            // ->preload()
            // ->searchable(),
            Forms\Components\Select::make('subject_id')
                ->label(__('inspections/inspection.form.fields.subject'))
                ->options(Vehicle::with(['codes', 'model'])
                    ->get()
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
