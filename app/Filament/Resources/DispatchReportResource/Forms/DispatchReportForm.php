<?php

namespace App\Filament\Resources\DispatchReportResource\Forms;

use App\Filament\Components\DepartmentPicker;
use App\Filament\Components\VehiclePicker;
use App\Models\Datahub\Department;
use App\Models\Datahub\EmployeeContract;
use App\Services\Inspection\TemplateAssignmentService;
use Carbon\Carbon;
use Dpb\Package\Activities\Models\ActivityTemplate;
use Dpb\Package\Fleet\Models\DispatchGroup;
use Dpb\Package\Fleet\Models\MaintenanceGroup;
use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Package\Inspections\Models\InspectionTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Get;
use Illuminate\Support\Collection;

class DispatchReportForm
{
    public static function make(Form $form): Form
    {
        return $form
            ->columns(6)
            ->schema([
                // date
                Forms\Components\DatePicker::make('date')
                    ->label(__('dispatch-report.form.fields.date'))
                    ->default(Carbon::now()),
                // inspection template
                Forms\Components\Select::make('vehicle_id')
                    ->label(__('dispatch-report.form.fields.subject'))
                    ->columnSpan(3)
                    // ->relationship('source', 'title', null, true)
                    ->options(fn() => Vehicle::pluck('code_1', 'id'))
                    ->preload()
                    ->searchable()
                    // ->disabled(fn($record) => $record->source_id == TicketSource::byCode('planned-maintenance')->first()->id)
                    ->required(false),
                Forms\Components\Textarea::make('description')
                    ->label(__('dispatch-report.form.fields.description'))
                    ->columnSpan(4)

            ]);
    }
}
