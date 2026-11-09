<?php

namespace Dpb\Modules\Inspections\Filament\Resources\Inspections\InspectionTemplateGroupResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;

class InspectionTemplateGroupTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->heading(__('dpb-mod-inspections::inspection-template-group.table.heading'))
            ->emptyStateHeading(__('dpb-mod-inspections::inspection-template-group.table.empty_state_heading'))
            // ->description(__('dpb-mod-inspections::inspection-template-group.table.description'))
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(25)
            ->columns([
                // code
                Tables\Columns\TextColumn::make('code')
                    ->label(__('dpb-mod-inspections::inspection-template-group.table.columns.code')),
                // title
                Tables\Columns\TextColumn::make('title')
                    ->label(__('dpb-mod-inspections::inspection-template-group.table.columns.title'))
                    ->searchable(),
                // description
                Tables\Columns\TextColumn::make('description')
                    ->label(__('dpb-mod-inspections::inspection-template-group.table.columns.description'))
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    // \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
