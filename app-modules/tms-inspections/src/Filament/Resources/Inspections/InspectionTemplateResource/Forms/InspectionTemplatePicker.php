<?php

namespace Dpb\Modules\Inspections\Filament\Resources\Inspections\InspectionTemplateResource\Forms;

use Filament\Forms;

class InspectionTemplatePicker
{
    private const OPTIONS_LIMIT = 2000;

    public static function make(string $uri)
    {
        return Forms\Components\Select::make($uri)
            ->label(__('dpb-mod-inspections::inspection-template.components.picker.label'))
            ->searchable()
            ->preload()
            ->optionsLimit(limit: self::OPTIONS_LIMIT)
            ->createOptionForm(InspectionTemplateForm::schema())
            ->createOptionModalHeading(__('dpb-mod-inspections::inspection-template.components.picker.create_heading'))
            ->editOptionForm(InspectionTemplateForm::schema())
            ->editOptionModalHeading(__('dpb-mod-inspections::inspection-template.components.picker.update_heading'));
    }
}
