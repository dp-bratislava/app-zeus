<?php

namespace App\Filament\Resources\Inspection\InspectionTemplateGroupResource\Tables;

use Filament\Tables;
use Filament\Tables\Table;

class InspectionTemplateGroupTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->heading(__('inspections/inspection-template-group.table.heading'))
            // ->description(__('inspections/inspection-template-group.table.description'))
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->columns([
                // code
                Tables\Columns\TextColumn::make('code')
                    ->label(__('inspections/inspection-template-group.table.columns.code')),
                // title
                Tables\Columns\TextColumn::make('title')
                    ->label(__('inspections/inspection-template-group.table.columns.title'))
                    ->searchable(),
                // description
                Tables\Columns\TextColumn::make('description')
                    ->label(__('inspections/inspection-template-group.table.columns.description'))
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
