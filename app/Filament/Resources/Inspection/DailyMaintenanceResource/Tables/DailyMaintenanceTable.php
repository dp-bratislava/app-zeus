<?php

namespace App\Filament\Resources\Inspection\DailyMaintenanceResource\Tables;

use App\Models\InspectionAssignment;
use App\States;
use App\UseCases\DailyMaintenance\CreateDailyMaintenanceUseCase;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;

class DailyMaintenanceTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->heading(__('inspections/daily-maintenance.table.heading'))
            ->emptyStateHeading(__('inspections/daily-maintenance.table.empty_state_heading'))
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->recordClasses(fn($record) => match ($record->inspection?->state?->getValue()) {
                States\Inspection\Upcoming::$name => 'bg-blue-200',
                States\Inspection\InProgress::$name => 'bg-yellow-200',
                default => null,
            })
            ->defaultSort('inspection.date', 'desc')
            ->columns([
                // date
                Tables\Columns\TextColumn::make('inspection.date')->date('j.n.Y')
                    ->label(__('inspections/daily-maintenance.table.columns.date'))
                    ->sortable(),
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
                    ->model(InspectionAssignment::class)                  
                    ->action(function (array $data, CreateDailyMaintenanceUseCase $uc) {
                        $uc->execute($data);
                        // return null;
                    })                    
                    ->modalWidth(MaxWidth::class)
                    ->modalDescription('TO DO: toto by malo do budúcna prepojené s fondom pracovného času')
                    ->modalHeading(__('inspections/daily-maintenance.create_heading')),
            ]);
    }
}
