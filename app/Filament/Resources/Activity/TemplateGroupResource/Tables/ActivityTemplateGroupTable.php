<?php

namespace App\Filament\Resources\Activity\ActivityTemplateGroupResource\Tables;

use App\Filament\Imports\Activity\ActivityTemplateImporter;
use Filament\Tables;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Table;

class ActivityTemplateGroupTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label(__('activities/activity-template-group.table.columns.code.label'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('activities/activity-template-group.table.columns.title.label'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('parent.title')
                    ->label(__('activities/activity-template-group.table.columns.parent.label')),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('activities/activity-template-group.table.columns.description.label')),

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
