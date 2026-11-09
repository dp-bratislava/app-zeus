<?php

namespace Dpb\Modules\Inspections\Filament\Resources\Inspections\InspectionTemplateResource\Tables;

use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;

class InspectionTemplateTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->heading(__('dpb-mod-inspections::inspection-template.table.heading'))
            ->emptyStateHeading(__('dpb-mod-inspections::inspection-template.table.empty_state_heading'))
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(25)
            ->columns([
                // // code
                // Tables\Columns\TextColumn::make('code')
                //     ->label(__('dpb-mod-inspections::inspection-template.table.columns.code.label')),
                // title
                Tables\Columns\TextColumn::make('title')
                    ->label(__('dpb-mod-inspections::inspection-template.table.columns.title.label'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('treshold_distance')
                    ->label(__('dpb-mod-inspections::inspection-template.table.columns.treshold_distance.label'))
                    ->tooltip(__('dpb-mod-inspections::inspection-template.table.columns.treshold_distance.tooltip')),
                // ->state(function ($record) {
                //     return $record->getCondition('treshold', 'distance_traveled')?->value;
                // }),
                Tables\Columns\TextColumn::make('first_advance_distance')
                    ->label(__('dpb-mod-inspections::inspection-template.table.columns.first_advance_distance.label'))
                    ->tooltip(__('dpb-mod-inspections::inspection-template.table.columns.first_advance_distance.tooltip')),
                // ->state(function ($record) {
                //     return $record->getCondition('1-advance', 'distance_traveled')?->value;
                // }),
                Tables\Columns\TextColumn::make('second_advance_distance')
                    ->label(__('dpb-mod-inspections::inspection-template.table.columns.second_advance_distance.label'))
                    ->tooltip(__('dpb-mod-inspections::inspection-template.table.columns.second_advance_distance.tooltip')),
                // ->state(function ($record) {
                //     return $record->getCondition('2-advance', 'distance_traveled')?->value;
                // }),
                Tables\Columns\TextColumn::make('treshold_time')
                    ->label(__('dpb-mod-inspections::inspection-template.table.columns.treshold_time.label')),
                Tables\Columns\TextColumn::make('first_advance_time')
                    ->label(__('dpb-mod-inspections::inspection-template.table.columns.first_advance_time.label')),
                Tables\Columns\TextColumn::make('second_advance_time')
                    ->label(__('dpb-mod-inspections::inspection-template.table.columns.second_advance_time.label')),
                // is periodic
                Tables\Columns\IconColumn::make('is_periodic')
                    ->label(__('dpb-mod-inspections::inspection-template.table.columns.is_periodic.label'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('groups.title')
                    ->label(__('dpb-mod-inspections::inspection-template.table.columns.groups.label')),
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
            ]);
        // ->bulkActions([
        //     \Filament\Actions\BulkActionGroup::make([
        //         \Filament\Actions\DeleteBulkAction::make(),
        //     ]),
        // ]);
    }
}
