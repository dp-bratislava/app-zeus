<?php

namespace App\Filament\Resources\Fleet\Vehicle\MaintenanceGroupResource\Tables;

use Filament\Tables;
use Filament\Tables\Table;

class MaintenanceGroupTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label(__('fleet/maintenance-group.table.columns.code')),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('fleet/maintenance-group.table.columns.title')),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('fleet/maintenance-group.table.columns.description')),
                Tables\Columns\ColorColumn::make('color')
                    ->label(__('fleet/maintenance-group.table.columns.color')),
            ])
            ->filters([
                //
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
