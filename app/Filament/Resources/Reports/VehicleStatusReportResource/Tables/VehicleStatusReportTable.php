<?php

namespace App\Filament\Resources\Reports\VehicleStatusReportResource\Tables;

use Dpb\Package\Fleet\Models\Vehicle;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class VehicleStatusReportTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->query(Vehicle::query()->where('code_1', '=', '1906'))
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->columns([
                Tables\Columns\TextColumn::make('TP')
                    ->label(__('reports/vehicle-status-report.table.columns.maintenance_group.label'))
                    ->tooltip(__('reports/vehicle-status-report.table.columns.maintenance_group.tooltip'))
                    ->state(function ($record) {
                        return $record->code->code[0] . 'TP';
                    }),
                    // model
                Tables\Columns\TextColumn::make('model.title')
                    ->label(__('reports/vehicle-status-report.table.columns.model'))
                    ->searchable()
                    ->sortable(),
                    // vehicle code
                Tables\Columns\TextColumn::make('code.code')
                    ->label(__('reports/vehicle-status-report.table.columns.code')),
                Tables\Columns\TextColumn::make('porucha'),
                // date from
                Tables\Columns\TextColumn::make('date_from')
                    ->label(__('reports/vehicle-status-report.table.columns.date_from.label'))
                    ->tooltip(__('reports/vehicle-status-report.table.columns.date_from.tooltip'))
                    ->date(),
                //     ->state(fn() => '2025-05-' . rand(10, 30)),
                Tables\Columns\TextColumn::make('activity'),
                Tables\Columns\TextColumn::make('predp cena'),
                    // ->state('7000,30 EUR'),
                Tables\Columns\TextColumn::make('ziad vyst dat')
                    ->date(),
                    // ->state('2025-05-03'),
                Tables\Columns\TextColumn::make('ziad cislo'),
                    // ->state('16/2025'),
                Tables\Columns\TextColumn::make('days_out_of_service')
                    ->label(__('reports/vehicle-status-report.table.columns.days_out_of_service.label'))
                    // ->state(function () {
                    //     return floor(Carbon::parse('2025-05-01')->diffInDays());
                    //     // return rand(1, 3);
                    // })
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        2 => 'warning',
                        1 => 'success',
                        3 => 'danger',
                        default => 'secondary',
                    }),

                Tables\Columns\TextColumn::make('rds')
                    ->label(__('reports/vehicle-status-report.table.columns.rds')),
                Tables\Columns\TextColumn::make('ocl')
                    ->label(__('reports/vehicle-status-report.table.columns.ocl')),
                Tables\Columns\TextColumn::make('plan dodania'),
                Tables\Columns\TextColumn::make('pozn'),
                Tables\Columns\TextColumn::make('stk/ek')
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
