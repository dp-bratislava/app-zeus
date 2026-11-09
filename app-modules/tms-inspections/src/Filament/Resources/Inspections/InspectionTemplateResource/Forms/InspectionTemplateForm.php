<?php

namespace Dpb\Modules\Inspections\Filament\Resources\Inspections\InspectionTemplateResource\Forms;

use Dpb\Package\Fleet\Models\VehicleModel;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class InspectionTemplateForm
{
    public static function make(Schema $schema): Schema
    {
        return $schema
            ->schema(static::schema())
            ->columns(6);
    }

    public static function schema(): array
    {
        return [
            // code
            Forms\Components\TextInput::make('code')
                ->label(__('dpb-mod-inspections::inspection-template.form.fields.code.label'))
                ->columnSpan(1),
            // title
            Forms\Components\TextInput::make('title')
                ->label(__('dpb-mod-inspections::inspection-template.form.fields.title.label'))
                ->required()
                ->columnSpan(2),
            // description
            Forms\Components\TextInput::make('description')
                ->label(__('dpb-mod-inspections::inspection-template.form.fields.description.label'))
                ->columnSpan(2),
            // is periodic
            Forms\Components\Checkbox::make('is_periodic')
                ->label(__('dpb-mod-inspections::inspection-template.form.fields.is_periodic.label'))
                ->columnSpan(1),
            // conditions - distance
            Section::make('')
                ->columns(1)
                ->columnSpan(2)
                ->schema([
                    // Forms\Components\TextInput::make('cnd_distance_treshold')
                    Forms\Components\TextInput::make('treshold_distance')
                        ->label(__('dpb-mod-inspections::inspection-template.form.fields.treshold_distance.label'))
                        ->hint(__('dpb-mod-inspections::inspection-template.form.fields.treshold_distance.hint'))
                        ->columnSpan(1),
                    // Forms\Components\TextInput::make('cnd_distance_1adv')
                    Forms\Components\TextInput::make('first_advance_distance')
                        ->label(__('dpb-mod-inspections::inspection-template.form.fields.first_advance_distance.label'))
                        ->hint(__('dpb-mod-inspections::inspection-template.form.fields.first_advance_distance.hint'))
                        ->columnSpan(1),
                    // Forms\Components\TextInput::make('cnd_distance_2adv')
                    Forms\Components\TextInput::make('second_advance_distance')
                        ->label(__('dpb-mod-inspections::inspection-template.form.fields.second_advance_distance.label'))
                        ->hint(__('dpb-mod-inspections::inspection-template.form.fields.second_advance_distance.hint'))
                        ->columnSpan(1),
                ]),
            // conditions - time
            Section::make('')
                ->columns(1)
                ->columnSpan(2)
                ->schema([
                    // Forms\Components\TextInput::make('cnd_time_treshold')
                    Forms\Components\TextInput::make('treshold_time')
                        ->label(__('dpb-mod-inspections::inspection-template.form.fields.treshold_time.label'))
                        ->hint(__('dpb-mod-inspections::inspection-template.form.fields.treshold_time.hint'))
                        ->columnSpan(1),
                    // Forms\Components\TextInput::make('cnd_time_1adv')
                    Forms\Components\TextInput::make('first_advance_time')
                        ->label(__('dpb-mod-inspections::inspection-template.form.fields.first_advance_time.label'))
                        ->hint(__('dpb-mod-inspections::inspection-template.form.fields.first_advance_time.hint'))
                        ->columnSpan(1),
                    // Forms\Components\TextInput::make('cnd_time_2adv')
                    Forms\Components\TextInput::make('second_advance_time')
                        ->label(__('dpb-mod-inspections::inspection-template.form.fields.second_advance_time.label'))
                        ->hint(__('dpb-mod-inspections::inspection-template.form.fields.second_advance_time.hint'))
                        ->columnSpan(1),
                ]),

            // // inspection template groups
            Forms\Components\CheckboxList::make('groups')
                ->label(__('dpb-mod-inspections::inspection-template.form.fields.groups.label'))
                ->relationship('groups', 'title')
                ->columnSpan(2),

            // templatables / vehicle models
            Tabs::make('Tabs')
                ->columnSpanFull()
                ->tabs([
                    // activity templates
                    // Tab::make(__('fleet/vehicle-model.form.tabs.activity-templates'))
                    // ->schema(ActivityTemplatesTab::make()),
                    // ticket item groups
                    Tab::make(__('dpb-mod-inspections::inspection-template.form.tabs.task_item_groups'))
                        // ->desc
                        ->schema(TaskItemGroupsTab::make()),
                    // vehicle models
                    Tab::make(__('dpb-mod-inspections::inspection-template.form.tabs.vehicle_models'))
                        ->schema(VehicleModelsTab::make()),
                ]),

            // Forms\Components\CheckboxList::make('vehicle_models')
            //     ->label(__('dpb-mod-inspections::inspection-template.form.fields.templatables.label'))
            //     ->hint(__('dpb-mod-inspections::inspection-template.form.fields.templatables.hint'))
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

    // private static function vehicleTypeField()
    // {
    //     return Forms\Components\ToggleButtons::make('vehicle_type')
    //         ->label(__('dpb-mod-inspections::daily-maintenance.form.fields.assigned_to.label'))
    //         // ->hint(__('dpb-mod-inspections::daily-maintenance.form.fields.assigned_to.hint'))
    //         ->options(
    //             fn() =>
    //             MaintenanceGroup::when(!auth()->user()->hasRole('super-admin'), function ($q) {
    //                 $userHandledVehicleTypes = auth()->user()->vehicleTypes();
    //                 $q->byVehicleType($userHandledVehicleTypes);
    //             })
    //                 ->pluck('code', 'id')
    //         )
    //         ->dehydrated()
    //         ->live()
    //         ->inline();
    // }
}
