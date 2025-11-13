<?php

namespace App\Filament\Resources\Fleet\MaintenanceGroupResource\Tables;

use Filament\Tables;
use Filament\Tables\Table;

class MaintenanceGroupTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->heading(__('fleet/maintenance-group.table.heading'))
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label(__('fleet/maintenance-group.table.columns.code')),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('fleet/maintenance-group.table.columns.title')),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('fleet/maintenance-group.table.columns.description')),
                // Tables\Columns\ColorColumn::make('color')
                //     ->label(__('fleet/maintenance-group.table.columns.color')),
                Tables\Columns\TextColumn::make('vehicleType.title')
                    ->label(__('fleet/maintenance-group.table.columns.vehicle_type')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
