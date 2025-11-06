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
                    ->state(function ($record) {
                        return $record->code->code[0] . 'TP';
                    }),
                Tables\Columns\TextColumn::make('model.title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code.code'),
                Tables\Columns\TextColumn::make('porucha'),
                Tables\Columns\TextColumn::make('date_from')
                    ->tooltip('datum zaradenia do spravky')
                    ->date()
                    ->state(fn() => '2025-05-' . rand(10, 30)),
                Tables\Columns\TextColumn::make('activity'),
                Tables\Columns\TextColumn::make('predp cena')
                    ->state('7000,30 EUR'),
                Tables\Columns\TextColumn::make('ziad vyst dat')
                    ->date()
                    ->state('2025-05-03'),
                Tables\Columns\TextColumn::make('ziad cislo')
                    ->state('16/2025'),
                Tables\Columns\TextColumn::make('v spravke dni')
                    ->state(function () {
                        return floor(Carbon::parse('2025-05-01')->diffInDays());
                        // return rand(1, 3);
                    })
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        2 => 'warning',
                        1 => 'success',
                        3 => 'danger',
                        default => 'secondary',
                    }),

                Tables\Columns\TextColumn::make('rds'),
                Tables\Columns\TextColumn::make('ocl'),
                Tables\Columns\TextColumn::make('plan dodania'),
                Tables\Columns\TextColumn::make('pozn'),
                Tables\Columns\TextColumn::make('stk/ek')
                    ->date()
                    ->state('2025-05-05'),

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
