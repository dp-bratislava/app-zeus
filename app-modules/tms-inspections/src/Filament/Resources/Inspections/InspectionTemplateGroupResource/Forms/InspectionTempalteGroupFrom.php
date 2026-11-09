<?php

namespace Dpb\Modules\Inspections\Filament\Resources\Inspections\InspectionTemplateGroupResource\Forms;

use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Modules\Inspections\Filament\Resources\Inspections\InspectionTemplateResource\Forms\InspectionTemplatePicker;
use Filament\Forms;
use Filament\Schemas\Schema;

class InspectionTempalteGroupFrom
{
    public static function make(Schema $schema): Schema
    {
        return $schema->schema(static::schema());
    }

    public static function schema(): array
    {
        return [
            // code
            Forms\Components\TextInput::make('code')
                ->label(__('dpb-mod-inspections::inspection-template-group.form.fields.code.label'))
                ->hint(__('dpb-mod-inspections::inspection-template-group.form.fields.code.hint')),
            // title
            Forms\Components\TextInput::make('title')
                ->columnSpan(1)
                ->label(__('dpb-mod-inspections::inspection-template-group.form.fields.title')),
            // description
            Forms\Components\TextInput::make('description')
                ->columnSpan(1)
                ->label(__('dpb-mod-inspections::inspection-template-group.form.fields.description')),
            // template
            // InspectionTemplatePicker::make('template_id')
            //     ->label(__('dpb-mod-inspections::inspection-assignment.form.fields.template'))
            //     ->relationship('template', 'title'),
            // // subject
            //     Forms\Components\Select::make('subject_id')
            //         ->label(__('dpb-mod-inspections::inspection-assignment.form.fields.subject'))
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
