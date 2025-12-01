<?php

namespace App\Filament\Resources\Fleet\Vehicle\VehicleTypeResource\Tables;

use Filament\Tables;
use Filament\Tables\Table;

class VehicleTypeTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->heading(__('fleet/vehicle-type.table.heading'))
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->columns([
                // code
                Tables\Columns\TextColumn::make('code')
                    ->label(__('fleet/vehicle-type.table.columns.code')),
                // title
                Tables\Columns\TextColumn::make('title')
                    ->label(__('fleet/vehicle-type.table.columns.title')),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // ->visible(auth()->user()->can('fleet.vehicle-model.update')),
                Tables\Actions\DeleteAction::make(),
                // ->visible(auth()->user()->can('fleet.vehicle-model.delete')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make()
                    //     ->visible(auth()->user()->can('fleet.vehicle-model.bulk-delete')),
                ]),
            ]);
    }
}
