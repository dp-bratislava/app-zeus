<?php

namespace App\Filament\Resources\Inspection\InspectionTemplateResource\Forms;

use App\Filament\Resources\Fleet\Vehicle\VehicleModelResource\Tables\VehicleModelTable;
use Dpb\Package\Fleet\Models\VehicleModel;
use Filament\Forms;
use Filament\Forms\Form;

class InspectionTemplateForm
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
            // code 
            Forms\Components\TextInput::make('code')
                ->label(__('inspections/inspection-template.form.fields.code.label'))
                ->columnSpan(1),
            // title
            Forms\Components\TextInput::make('title')
                ->label(__('inspections/inspection-template.form.fields.title.label'))
                ->columnSpan(2),
            // description 
            Forms\Components\TextInput::make('description')
                ->label(__('inspections/inspection-template.form.fields.description.label'))
                ->columnSpan(3),
            // conditions - distance
            Forms\Components\Section::make('')
                ->columns(1)
                ->columnSpan(2)
                ->schema([
                    // Forms\Components\TextInput::make('cnd_distance_treshold')
                    Forms\Components\TextInput::make('treshold_distance')
                        ->label(__('inspections/inspection-template.form.fields.treshold_distance.label'))
                        ->hint(__('inspections/inspection-template.form.fields.treshold_distance.hint'))
                        ->columnSpan(1),
                    // Forms\Components\TextInput::make('cnd_distance_1adv')
                    Forms\Components\TextInput::make('first_advance_distance')
                        ->label(__('inspections/inspection-template.form.fields.first_advance_distance.label'))
                        ->hint(__('inspections/inspection-template.form.fields.first_advance_distance.hint'))
                        ->columnSpan(1),
                    // Forms\Components\TextInput::make('cnd_distance_2adv')
                    Forms\Components\TextInput::make('second_advance_distance')
                        ->label(__('inspections/inspection-template.form.fields.second_advance_distance.label'))
                        ->hint(__('inspections/inspection-template.form.fields.second_advance_distance.hint'))
                        ->columnSpan(1),
                ]),
            // conditions - time
            Forms\Components\Section::make('')
                ->columns(1)
                ->columnSpan(2)
                ->schema([
                    // Forms\Components\TextInput::make('cnd_time_treshold')
                    Forms\Components\TextInput::make('treshold_time')
                        ->label(__('inspections/inspection-template.form.fields.treshold_time.label'))
                        ->hint(__('inspections/inspection-template.form.fields.treshold_time.hint'))
                        ->columnSpan(1),
                    // Forms\Components\TextInput::make('cnd_time_1adv')
                    Forms\Components\TextInput::make('first_advance_time')
                        ->label(__('inspections/inspection-template.form.fields.first_advance_time.label'))
                        ->hint(__('inspections/inspection-template.form.fields.first_advance_time.hint'))
                        ->columnSpan(1),
                    // Forms\Components\TextInput::make('cnd_time_2adv')
                    Forms\Components\TextInput::make('second_advance_time')
                        ->label(__('inspections/inspection-template.form.fields.second_advance_time.label'))
                        ->hint(__('inspections/inspection-template.form.fields.second_advance_time.hint'))
                        ->columnSpan(1),
                ]),

            // inspection template groups
            Forms\Components\CheckboxList::make('groups')
                ->label(__('inspections/inspection-template.form.fields.groups.label'))
                ->relationship('groups', 'title')
                ->columnSpan(2),

            // templatables / vehicle models 
                Forms\Components\Tabs::make('Tabs')
                    ->columnSpanFull()
                    ->tabs([
                        // activity templates
                        // Forms\Components\Tabs\Tab::make(__('fleet/vehicle-model.form.tabs.activity-templates'))
                        // ->schema(ActivityTemplatesTab::make()),
                        // ticket item groups
                        Forms\Components\Tabs\Tab::make(__('inspections/inspection-template.form.tabs.ticket_item_groups'))
                            // ->dess
                            ->schema(TicketItemGroupsTab::make()),
                        // vehicle models
                        Forms\Components\Tabs\Tab::make(__('inspections/inspection-template.form.tabs.vehicle_models'))
                            ->schema(VehicleModelsTab::make()),
                    ]),

            // Forms\Components\CheckboxList::make('vehicle_models')
            //     ->label(__('inspections/inspection-template.form.fields.templatables.label'))
            //     ->hint(__('inspections/inspection-template.form.fields.templatables.hint'))
            //     ->options(function () {
            //         return VehicleModel::get()
            //             ->mapWithKeys(fn($vm) => [$vm->id => $vm->title]);
            //     })
            //     ->bulkToggleable()
            //     ->searchable()
            //     ->columns(6)
            //     ->columnSpanFull()

        ];
    }
}
