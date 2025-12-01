<?php

namespace App\Filament\Resources\Incident\IncidentTypeResource\Tables;

use Filament\Tables;
use Filament\Tables\Table;

class IncidentTypeTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->heading(__('incidents/incident-type.table.heading'))
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->columns([
                // code
                Tables\Columns\TextColumn::make('code')
                    ->label(__('incidents/incident-type.table.columns.code.label')),
                // title
                Tables\Columns\TextColumn::make('title')
                    ->label(__('incidents/incident-type.table.columns.title.label')),
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
            ]);
        // ->bulkActions([
        //     Tables\Actions\BulkActionGroup::make([
        //         Tables\Actions\DeleteBulkAction::make(),
        //     ]),
        // ]);
    }
}
