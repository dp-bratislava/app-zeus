<?php

namespace App\Filament\Resources\Inspection\DailyMaintenanceResource\Forms;

use App\Filament\Components\DepartmentPicker;
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

class DailyMaintenanceForm
{
    public static function make(Form $form): Form
    {
        return $form
            ->columns(6)
            ->schema([
                // date
                Forms\Components\DatePicker::make('date')
                    ->label(__('inspections/daily-maintenance.form.fields.date'))
                    ->default(Carbon::now())
                    ->columnSpan(1),
                // inspection template
                Forms\Components\ToggleButtons::make('inspection-template')
                    ->label(__('inspections/daily-maintenance.form.fields.template'))
                    ->inline()
                    ->options(
                        fn() =>
                        InspectionTemplate::whereIn('title', [
                            'Odstavná plocha',
                            'Pristavovanie vozidla',
                            'Programovanie',
                            'Strojové čistenie vozidla',
                        ])
                            ->pluck('title', 'id')
                    )
                    ->live()
                    ->columnSpan(5),
                Forms\Components\Split::make([
                    // vehicles
                    Forms\Components\Section::make([
                        Forms\Components\CheckboxList::make('vehicles')
                            ->label(__('inspections/daily-maintenance.form.fields.vehicles'))
                            ->options(fn() => Vehicle::limit(10)->pluck('code_1', 'id'))
                            ->searchable()
                            ->bulkToggleable()
                            ->columns(3)
                            ->columnSpan(2),
                    ]),
                    // contracts
                    Forms\Components\Section::make([
                        Forms\Components\CheckboxList::make('contracts')
                            ->label(__('inspections/daily-maintenance.form.fields.contracts'))
                            ->options(fn(): Collection => EmployeeContract::workers()->byDepartment('2516')->pluck('pid', 'id'))
                            ->searchable()
                            ->bulkToggleable()
                            ->columns(3)
                            ->columnSpan(2),
                    ]),
                    // activites
                    Forms\Components\Section::make([
                        Forms\Components\CheckboxList::make('activity-templates')
                            ->label(__('inspections/daily-maintenance.form.fields.activity-templates'))
                            ->options(function (Get $get, TemplateAssignmentService $tplAssignmentSvc, InspectionTemplate $inspectionTemplate, ActivityTemplate $activityTemplate): Collection {
                                // dd($activityTemplate->getMorphClass());
                                if ($get('inspection-template') == null) {
                                    return collect([]);
                                }
                                return $tplAssignmentSvc
                                    ->getSubjectsByTemplate(
                                        // $inspectionTemplate->where('title', '=', 'Strojové čistenie vozidla')->first(),
                                        $inspectionTemplate->find($get('inspection-template')),
                                        [$activityTemplate->getMorphClass()]
                                    )
                                    ->pluck('title', 'id');
                            })
                            ->searchable()
                            ->bulkToggleable()
                            ->columns(2)
                            ->columnSpan(2),
                    ])
                        ->columnSpan(2),
                ])
                    ->columnSpanFull()
            ]);
    }
}
