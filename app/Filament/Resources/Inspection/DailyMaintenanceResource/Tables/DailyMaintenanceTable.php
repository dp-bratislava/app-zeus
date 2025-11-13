<?php

namespace App\Filament\Resources\Inspection\DailyMaintenanceResource\Tables;

use App\Services\Fleet\VehicleService;
use App\Services\Inspection\CreateTicketService;
use App\Services\Inspection\AssignmentService as InspectionAssignmentService;
use App\States;
use Dpb\Package\Inspections\Models\Inspection;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class DailyMaintenanceTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->recordClasses(fn($record) => match ($record->state?->getValue()) {
                States\Inspection\Upcoming::$name => 'bg-blue-200',
                States\Inspection\InProgress::$name => 'bg-yellow-200',
                default => null,
            })
            ->columns([
                Tables\Columns\TextColumn::make('date')->date('j.n.Y')
                    ->label(__('inspections/daily-maintenance.table.columns.date.label')),
                Tables\Columns\TextColumn::make('subject')
                    ->label(__('inspections/daily-maintenance.table.columns.subject.label'))
                    ->state(function ($record, InspectionAssignmentService $svc) {
                        return $svc->getSubject($record)?->code?->code;
                    }),
                Tables\Columns\TextColumn::make('template.title')
                    ->label(__('inspections/daily-maintenance.table.columns.template.label')),
                Tables\Columns\TextColumn::make('state')
                    ->label(__('inspections/daily-maintenance.table.columns.state.label'))
                    ->state(fn(Inspection $record) => $record?->state?->label()),
                Tables\Columns\TextColumn::make('subject.maintenanceGroup.title')
                    ->label(__('inspections/daily-maintenance.table.columns.maintenance_group.label')),
                Tables\Columns\TextColumn::make('note')
                    ->label(__('inspections/daily-maintenance.table.columns.note.label')),
                Tables\Columns\TextColumn::make('total_time')
                    ->label(__('inspections/daily-maintenance.table.columns.total_time.label')),
                Tables\Columns\TextColumn::make('contracts')
                    ->label(__('inspections/daily-maintenance.table.columns.contracts.label')),
            ])
            ->filters([
                //

            ]);
    }
}
