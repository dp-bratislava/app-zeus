<?php

namespace App\Filament\Resources\Inspection\InspectionTemplateResource\Forms;

use Filament\Forms;

class InspectionTemplatePicker
{
    public static function make(string $uri)
    {
        return Forms\Components\Select::make($uri)
            ->label(__('inspections/inspection-template.components.picker.label'))
            ->searchable()
            ->preload()
            ->createOptionForm(InspectionTemplateForm::schema())
            ->createOptionModalHeading(__('inspections/inspection-template.components.picker.create_heading'))
            ->editOptionForm(InspectionTemplateForm::schema())
            ->editOptionModalHeading(__('inspections/inspection-template.components.picker.update_heading'));
    }
}
