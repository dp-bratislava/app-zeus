<?php

namespace App\Filament\Resources\Activity\ActivityTemplateResource\Forms;

use Dpb\Package\Fleet\Models\VehicleModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;

class ActivityTemplateForm
{
    public static function make(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label(__('activities/activity-template.form.fields.code.label')),
                Forms\Components\TextInput::make('title')
                    ->label(__('activities/activity-template.form.fields.title')),
                Forms\Components\TextInput::make('duration')
                    ->label(__('activities/activity-template.form.fields.duration.label'))
                    ->numeric(),

                Forms\Components\Checkbox::make('is_divisible')
                    ->label(__('activities/activity-template.form.fields.is_divisible'))
                    ->live(),
                Forms\Components\Checkbox::make('is_catalogised')
                    ->label(__('activities/activity-template.form.fields.is_catalogised')),
                Forms\Components\TextInput::make('people')
                    ->label(__('activities/activity-template.form.fields.people'))
                    ->numeric()
                    ->visible(fn(Get $get) => $get('is_divisible')),

                // subject
                Forms\Components\Select::make('templatable_id')
                    ->label(__('activities/activity-template.form.fields.templatable'))
                    ->options(fn() => VehicleModel::pluck('title', 'id'))
                    ->preload()
                    ->searchable(),
                // // subjects
                // Forms\Components\Section::make('activities/activity-template.form.sections.subjects')
                //     ->schema([
                //         Forms\Components\CheckboxList::make('vehicle_models')
                //             ->label(__('activities/activity-template.form.fields.subjects'))
                //             ->bulkToggleable()
                //             ->searchable()
                //             ->columns(4)
                //             ->options(fn() => VehicleModel::pluck('title', 'id'))
                //     ])
            ]);
    }
}
