<?php

namespace App\Filament\Resources\TS\TicketSourceResource\Tables;

use Filament\Tables;
use Filament\Tables\Table;

class TicketSourceTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label(__('tickets/ticket-source.table.columns.code.label'))
                    ->tooltip(__('tickets/ticket-source.table.columns.code.tooltip')),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('tickets/ticket-source.table.columns.title.label')),
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
