<?php

namespace App\Filament\Resources\Activity\ActivityTemplateResource\Tables;

use App\Filament\Imports\Activity\ActivityTemplateImporter;
use App\Filament\Imports\Fleet\VehicleModelImporter;
use Filament\Tables;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Table;

class ActivityTemplateTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('duration'),
                Tables\Columns\TextColumn::make('man_minutes'),
                Tables\Columns\IconColumn::make('is_divisible')
                    ->label('delitelna')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_standardised')
                    ->label('normovana')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_catalogised')
                    ->label('katalogizovana')
                    ->boolean(),
                Tables\Columns\TextColumn::make('people')
                    ->label('pocet ludi'),
                Tables\Columns\TextColumn::make('sa1')
                    ->label('sa1')
                    ->state(function($record, UnitRateService $svc) {                        

                        return $svc->getUnitRate($record)?->formatted_rate;
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(ActivityTemplateImporter::class)
                    ->csvDelimiter(';')
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                    // ->visible(auth()->user()->can('fleet.vehicle-model.update')),
                Tables\Actions\DeleteAction::make()
                    // ->visible(auth()->user()->can('fleet.vehicle-model.delete')),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make()
                //         ->visible(auth()->user()->can('fleet.vehicle-model.bulk-delete')),
                // ]),
            ]);
    }

}
