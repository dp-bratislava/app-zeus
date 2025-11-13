<?php

namespace App\Filament\Resources\Fleet\Vehicle\VehicleGroupResource\Tables;

use Filament\Tables;
use Filament\Tables\Table;

class VehicleGroupTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label(__('fleet/vehicle-group.table.columns.code')),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('fleet/vehicle-group.table.columns.title')),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('fleet/vehicle-group.table.columns.description')),
            ])
            ->filters([
                //
            ])
            // ->headerActions([
            //     ImportAction::make()
            //         ->importer(VehicleGroupImporter::class)
            //         ->csvDelimiter(';')
            //         // ->visible(auth()->user()->can('fleet.vehicle-group.import'))
            // ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // ->visible(auth()->user()->can('fleet.vehicle-group.update')),
                Tables\Actions\DeleteAction::make()
                // ->visible(auth()->user()->can('fleet.vehicle-group.delete')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                    // ->visible(auth()->user()->can('fleet.vehicle-group.bulk-delete')),
                ]),
            ]);
    }
}
