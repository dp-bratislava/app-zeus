<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas;

use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components\MaintenanceGroupFilter;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components\VehiclePicker;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Components\VehicleTypeFilter;
use Dpb\Package\Fleet\Models\VehicleType;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class TaskSubjectTab
{
    public static function make(): Component
    {
        return Tab::make(__('dpb-mod-tasks::task-batch.form.tabs.subjects'))
            ->schema([
                Grid::make(6)
                    ->columnSpanFull()
                    ->schema([
                        MaintenanceGroupFilter::make(
                            UniformTaskBatchSchema::MAINTENANCE_GROUP_FILTER_KEY
                        )
                            ->afterStateUpdated(function (Set $set) {
                                $set(UniformTaskBatchSchema::VEHICLE_TYPE_FILTER_KEY, null);
                            })
                            ->hidden(function (Get $get) {
                                $typeId = $get(UniformTaskBatchSchema::VEHICLE_TYPE_FILTER_KEY);
                                if ($typeId) {
                                    $vehicleType = VehicleType::find($typeId);
                                    return $vehicleType && in_array($vehicleType->title, ['Električka', 'Trolejbus']);
                                }
                                return false;
                            }),
                        VehicleTypeFilter::make(
                            UniformTaskBatchSchema::VEHICLE_TYPE_FILTER_KEY
                        )
                            ->columnSpan(4)
                            ->afterStateUpdated(function (Set $set) {
                                $set(UniformTaskBatchSchema::MAINTENANCE_GROUP_FILTER_KEY, null);
                            }),
                    ]),
                Grid::make(1)
                    ->columnSpanFull()
                    ->schema([
                        VehiclePicker::make(
                            TaskBatchForm::SUBJECT_PICKER_KEY
                        )
                            ->label(__('dpb-mod-tasks::task-batch.form.fields.vehicles'))
                    ]),
            ]);
    }
}
