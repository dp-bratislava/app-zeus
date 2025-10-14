<?php

namespace App\Filament\Resources\Inspection\InspectionTemplateResource\Tables;

use App\States;
use Dpb\Package\Inspections\Models\InspectionTemplate;
use Filament\Tables;
use Filament\Tables\Table;

class InspectionTemplateTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label(__('inspections/inspection-template.table.columns.code.label')),
                Tables\Columns\TextColumn::make('title')                
                    ->label(__('inspections/inspection-template.table.columns.title.label')),

                Tables\Columns\TextColumn::make('interval_distance')
                    ->label(__('inspections/inspection-template.table.columns.interval_distance.label'))
                    ->state(function($record) {
                        return $record->conditions()->where('code', '=', 'treshold')->first()?->value;
                    }),
                Tables\Columns\TextColumn::make('first_advance_distance')
                    ->label(__('inspections/inspection-template.table.columns.first_advance_distance.label'))
                    ->state(function($record) {
                        return $record->conditions()->where('code', '=', '1-advance')->first()?->value;
                    }),
                Tables\Columns\TextColumn::make('second_advance_distance')
                    ->label(__('inspections/inspection-template.table.columns.second_advance_distance.label'))
                    ->state(function($record) {
                        return $record->conditions()->where('code', '=', '2-advance')->first()?->value;
                    }),
                Tables\Columns\TextColumn::make('interval_time')
                    ->label(__('inspections/inspection-template.table.columns.interval_time.label')),
                Tables\Columns\TextColumn::make('first_advance_time')
                    ->label(__('inspections/inspection-template.table.columns.first_advance_time.label')),
                Tables\Columns\TextColumn::make('second_advance_time')
                    ->label(__('inspections/inspection-template.table.columns.second_advance_time.label')),

                Tables\Columns\IconColumn::make('is_periodic')
                    ->label(__('inspections/inspection-template.table.columns.is_periodic.label'))
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
