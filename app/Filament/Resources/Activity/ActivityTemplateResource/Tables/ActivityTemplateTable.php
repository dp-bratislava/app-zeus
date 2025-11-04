<?php

namespace App\Filament\Resources\Activity\ActivityTemplateResource\Tables;

use App\Filament\Imports\Activity\ActivityTemplateImporter;
use App\Models\ActivityTemplateAssignment;
use Dpb\Package\Fleet\Models\VehicleModel;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ActivityTemplateTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('activities/activity-template.table.columns.title.label'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('duration')
                    ->label(__('activities/activity-template.table.columns.duration.label')),
                Tables\Columns\TextColumn::make('man_minutes')
                    ->label(__('activities/activity-template.table.columns.man_minutes.label')),
                Tables\Columns\IconColumn::make('is_divisible')
                    ->label(__('activities/activity-template.table.columns.is_divisible.label'))
                    ->boolean(),
                // Tables\Columns\IconColumn::make('is_standardised')
                //     ->label(__('activities/activity-template.table.columns..label'))
                //     ->boolean(),
                Tables\Columns\IconColumn::make('is_catalogised')
                    ->label(__('activities/activity-template.table.columns.is_catalogised.label'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('people')
                    ->label(__('activities/activity-template.table.columns.people.label')),
                // Tables\Columns\TextColumn::make('sa1')
                //     ->label('sa1')
                //     ->state(function($record, UnitRateService $svc) {                        

                //         return $svc->getUnitRate($record)?->formatted_rate;
                //     }),
                Tables\Columns\TextColumn::make('subject')
                    ->label('subj')
                    ->state(fn($record, ActivityTemplateAssignment $activityTemplateAssignment) => $activityTemplateAssignment->where('template_id', '=', $record->id)->with('subject')->get()->pluck('subject.title'))
            ])
            ->filters([
                // Tables\Filters\SelectFilter::make('subject')
                //     ->options(fn(VehicleModel $vehicleModel) => $vehicleModel::pluck('title', 'id')) 
                //     ->
                //     ->searchable()
                //     ->preload()
                //     ->multiple()
            ])
            ->headerActions([
                Tables\Actions\ImportAction::make()
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
