<?php

namespace App\Filament\Resources\Inspection\InspectionResource\Forms;

use App\Filament\Resources\Inspection\InspectionTemplateResource\Forms\InspectionTemplatePicker;
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
                ->label(__('inspections/inspection.form.fields.date')),
            // template
            InspectionTemplatePicker::make('template_id')
                ->relationship('template', 'title'),
            // Forms\Components\TextInput::make('description')
            //     ->columnSpan(1)
            //     ->label(__('fleet/maintenance-group.form.fields.description')),
            // Forms\Components\ColorPicker::make('color')
            //     ->columnSpan(1)
            //     ->label(__('fleet/maintenance-group.form.fields.color')),
        ];
    }
}
