<?php

namespace App\Filament\Resources\Reports\VehicleStatusReportResource\Tables;

use Dpb\Package\Fleet\Models\Vehicle;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class DailyExpeditionStatusReportTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->heading(__('reports/vehicle-status-report.table.heading'))
            ->description(__('reports/vehicle-status-report.table.description'))
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->columns([
                Tables\Columns\TextColumn::make('vehicle.maintenanceGroup.code')
                    ->label(__('reports/vehicle-status-report.table.columns.maintenance_group.label'))
                    ->tooltip(__('reports/vehicle-status-report.table.columns.maintenance_group.tooltip')),
                // ->state(function ($record) {
                //     return $record->maintenanceGroup?->code;
                // }),
                // vehicle code
                Tables\Columns\TextColumn::make('vehicle.code.code')
                    ->label(__('reports/vehicle-status-report.table.columns.code')),
                // model
                Tables\Columns\TextColumn::make('vehicle.model.title')
                    ->label(__('reports/vehicle-status-report.table.columns.model'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('porucha'),
                // out of service from date
                Tables\Columns\TextColumn::make('outOfServiceFrom')
                    ->label(__('reports/vehicle-status-report.table.columns.date_from.label'))
                    ->tooltip(__('reports/vehicle-status-report.table.columns.date_from.tooltip'))
                    ->date('j.m.Y'),
                //     ->state(fn() => '2025-05-' . rand(10, 30)),
                // Tables\Columns\TextColumn::make('activity'),
                // Tables\Columns\TextColumn::make('predp cena'),
                // ->state('7000,30 EUR'),
                // Tables\Columns\TextColumn::make('ziad vyst dat')
                //     ->date(),
                // ->state('2025-05-03'),
                // Tables\Columns\TextColumn::make('ziad cislo'),
                // ->state('16/2025'),
                // days out of service
                Tables\Columns\TextColumn::make('outOfServiceDays')
                    ->label(__('reports/vehicle-status-report.table.columns.days_out_of_service.label'))
                    // ->state(function () {
                    //     return floor(Carbon::parse('2025-05-01')->diffInDays());
                    //     // return rand(1, 3);
                    // })
                    ->badge()
                    ->color(function($state) {
                        if ($state > 30) { return 'danger'; }
                        elseif (($state > 10)) { return 'warning'; }
                        elseif (($state <= 10)) { return 'success'; }

                        return null;
                    }),

                // Tables\Columns\TextColumn::make('rds')
                //     ->label(__('reports/vehicle-status-report.table.columns.rds')),
                // Tables\Columns\TextColumn::make('ocl')
                //     ->label(__('reports/vehicle-status-report.table.columns.ocl')),
                // Tables\Columns\TextColumn::make('plan dodania'),
                // 
                Tables\Columns\TextColumn::make('note')
                    ->label(__('reports/vehicle-status-report.table.columns.note')),
                // closest inspections
                Tables\Columns\TextColumn::make('closest_inspections')
                    ->label(__('reports/vehicle-status-report.table.columns.closest_inspections.label'))
                    ->tooltip(__('reports/vehicle-status-report.table.columns.closest_inspections.tooltip'))
                    ->date('j.n.Y'),
                // ->state('2025-05-05'),

            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
