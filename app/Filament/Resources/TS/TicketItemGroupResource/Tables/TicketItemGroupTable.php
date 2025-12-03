<?php

namespace App\Filament\Resources\TS\TicketItemGroupResource\Tables;

use Filament\Tables;
use Filament\Tables\Table;

class TicketItemGroupTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->heading(__('tickets/ticket-item-group.table.heading'))
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->columns([
                // code
                Tables\Columns\TextColumn::make('code')
                    ->label(__('tickets/ticket-item-group.table.columns.code.label'))
                    ->tooltip(__('tickets/ticket-item-group.table.columns.code.tooltip')),
                // title
                Tables\Columns\TextColumn::make('title')
                    ->label(__('tickets/ticket-item-group.table.columns.title')),
                // parent
                Tables\Columns\TextColumn::make('parent.title')
                    ->label(__('tickets/ticket-item-group.table.columns.parent')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // ImportAction::make()
                //     ->importer(TicketItemGroupImporter::class)
                //     ->csvDelimiter(';')
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
