<?php

namespace App\Filament\Resources\Fleet\Vehicle\VehicleModelResource\Tables;

use App\Filament\Imports\Fleet\VehicleModelImporter;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Table;

class VehicleModelTable extends Resource
{
    public static function make(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('fleet/vehicle-model.table.columns.title.label')),
                Tables\Columns\TextColumn::make('year')
                    ->label(__('fleet/vehicle-model.table.columns.year.label')),
                Tables\Columns\TextColumn::make('length')
                    ->label(__('fleet/vehicle-model.table.columns.length.label'))
                    ->state(function ($record) {
                        // dd($record->attributeValues());
                        return $record->getAttrValue('length');
                    })
                    ->numeric(decimalPlaces: 2),

                // TextColumn::make('seats'),
                // TextColumn::make('fuel_consumption'),
                // TextColumn::make('length')
                //     ->state(function ($record) {
                //         return $record->getAttrValue('length');
                //     }),
                // TextColumn::make('warranty'),
                Tables\Columns\TextColumn::make('type.title')
                    ->label(__('fleet/vehicle-model.table.columns.type.label')),
                // TextColumn::make('fuelType.title'),
                // TextColumn::make('seats')
                //     ->state(function ($record) {
                //         return $record->getAttrValue('seat-capacity');
                //     }),
                // ->state(function($record) {return print_r($record->attributeValues());}),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(VehicleModelImporter::class)
                    ->csvDelimiter(';')
                    ->visible(auth()->user()->can('fleet.vehicle-model.import'))
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(auth()->user()->can('fleet.vehicle-model.update')),
                Tables\Actions\DeleteAction::make()
                    ->visible(auth()->user()->can('fleet.vehicle-model.delete')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(auth()->user()->can('fleet.vehicle-model.bulk-delete')),
                ]),
            ]);
    }

}
