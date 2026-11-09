<?php

namespace Dpb\Modules\Inspections\Filament\Resources\InspectionAssignments\Schemas;

use Dpb\Modules\Inspections\Filament\Resources\Inspections\InspectionTemplateResource\Forms\InspectionTemplatePicker;
use Carbon\Carbon;
use Dpb\Modules\Inspections\Filament\Resources\InspectionAssignments\Components\FleetVehiclePicker;
use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Package\TaskMS\Repositories\DepartmentAssignmentRepository;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Builder;

class InspectionAssignmentFrom
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema(static::schema())
            ->columns(4);
    }

    public static function schema(): array
    {
        return [
            // date
            Forms\Components\DatePicker::make('date')
                ->label(__('dpb-mod-inspections::inspection-assignment.form.fields.date'))
                ->columnSpan(1)
                ->default(Carbon::now()),
            // subject
            self::subjectField(),
            // template
            InspectionTemplatePicker::make('template_id')
                ->label(__('dpb-mod-inspections::inspection-assignment.form.fields.template'))
                ->columnSpan(2)
                ->relationship('inspection.template', 'title', function (Builder $query, Get $get) {
                    return $query;
                })
                ->required()
                ->key('templateIdField'),
        ];
    }

    private static function subjectField()
    {
        return FleetVehiclePicker::make('subject_id')
            ->label(__('dpb-mod-inspections::inspection-assignment.form.fields.subject'))
            ->columnSpan(1)
            ->options(function (DepartmentAssignmentRepository $daRepo) {
                $vtIds = $daRepo->getVehicleTypeIdsByActiveDepartment();
                return Vehicle::byTypeIds($vtIds)
                    ->get()
                    ->mapWithKeys(function ($vehicle) {
                        return [
                            $vehicle->id => $vehicle->label . ' - ' . $vehicle->model?->title
                        ];
                    });
            })
            ->getOptionLabelFromRecordUsing(null)
            ->getSearchResultsUsing(null)
            ->preload()
            ->searchable()
            ->required()
            ->live();
    }
}
