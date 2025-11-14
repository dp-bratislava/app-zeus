<?php

namespace App\Filament\Resources\TS\TicketGroupResource\Tables;

use Filament\Tables;
use Filament\Tables\Table;

class TicketGroupTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label(__('tickets/ticket-group.table.columns.code.label'))
                    ->tooltip(__('tickets/ticket-group.table.columns.code.tooltip')),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('tickets/ticket-group.table.columns.title')),
                Tables\Columns\TextColumn::make('parent.title')
                    ->label(__('tickets/ticket-group.table.columns.parent')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // ImportAction::make()
                //     ->importer(TicketGroupImporter::class)
                //     ->csvDelimiter(';')
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
