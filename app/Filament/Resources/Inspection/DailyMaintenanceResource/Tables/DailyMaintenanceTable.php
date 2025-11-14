<?php

namespace App\Filament\Resources\Inspection\DailyMaintenanceResource\Tables;

use App\Models\InspectionAssignment;
use App\Services\Fleet\VehicleService;
use App\Services\Inspection\CreateTicketService;
use App\Services\Inspection\AssignmentService as InspectionAssignmentService;
use App\Services\TS\TicketAssignmentService;
use App\States;
use Dpb\Package\Inspections\Models\Inspection;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class DailyMaintenanceTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->heading(__('inspections/daily-maintenance.table.heading'))
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->recordClasses(fn($record) => match ($record->inspection?->state?->getValue()) {
                States\Inspection\Upcoming::$name => 'bg-blue-200',
                States\Inspection\InProgress::$name => 'bg-yellow-200',
                default => null,
            })
            ->columns([
                // date
                Tables\Columns\TextColumn::make('inspection.date')->date('j.n.Y')
                    ->label(__('inspections/daily-maintenance.table.columns.date')),
                // subject
                Tables\Columns\TextColumn::make('subject.code.code')
                    ->label(__('inspections/daily-maintenance.table.columns.subject')),
                // ->state(function ($record, InspectionAssignmentService $svc) {
                //     return $svc->getSubject($record)?->code?->code;
                // }),
                // template
                Tables\Columns\TextColumn::make('inspection.template.title')
                    ->label(__('inspections/daily-maintenance.table.columns.template')),
                // state
                Tables\Columns\TextColumn::make('inspection.state')
                    ->label(__('inspections/daily-maintenance.table.columns.state'))
                    ->state(fn(InspectionAssignment $record) => $record->inspection?->state?->label()),
                // maintenance group
                Tables\Columns\TextColumn::make('subject.maintenanceGroup.code')
                    ->label(__('inspections/daily-maintenance.table.columns.maintenance_group'))
                    ->badge(),
                // TO DO
                // note
                Tables\Columns\TextColumn::make('note')
                    ->label(__('inspections/daily-maintenance.table.columns.note')),
                // total time
                Tables\Columns\TextColumn::make('total_time')
                    ->label(__('inspections/daily-maintenance.table.columns.total_time')),
                // contracts
                Tables\Columns\TextColumn::make('contracts')
                    ->label(__('inspections/daily-maintenance.table.columns.contracts')),
            ])
            ->filters(DailyMaintenanceTableFilters::make())
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->using(function (array $data, TicketAssignmentService $taSvc) {
                        $taSvc->createFromDailyMaintenance($data);
                    })
                    ->modalWidth(MaxWidth::class)
                    ->modalHeading(__('inspections/daily-maintenance.create_heading')),
            ]);
    }
}
