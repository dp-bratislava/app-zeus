<?php

namespace App\Filament\Resources\Activity\ActivityResource\Tables;

use Filament\Tables;
use Filament\Tables\Table;

class ActivityAssignmentTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->columns([
                // ticket
                Tables\Columns\TextColumn::make('subject.ticket.id')
                    ->label(__('activities/activity.table.columns.ticket')),
                // ->state(function(TicketService $svc, $record) {
                //     return $svc->getTicket($record)?->description;
                // }),
                // date
                Tables\Columns\TextColumn::make('activity.date')
                    ->label(__('activities/activity.table.columns.date'))
                    ->date(),
                Tables\Columns\TextColumn::make('activity.status.title'),
                // template
                Tables\Columns\TextColumn::make('activity.template.title')
                    ->label(__('activities/activity.table.columns.template')),
                // expected duration
                Tables\Columns\TextColumn::make('activity.template.duration')
                    ->label(__('activities/activity.table.columns.expected_duration')),
                // Tables\Columns\TextColumn::make('real_duration')
                //     ->label('realne trvanie')
                //     ->state(function ($record) {
                //         $result = $record->activities->sum('duration');
                //         return $result;
                //     }),
                // Tables\Columns\IconColumn::make('template.is_divisible')
                //     ->label('delitelna')
                //     ->boolean(),
                // Tables\Columns\IconColumn::make('template.is_standardised')
                //     ->label('normovana')
                //     ->boolean(),
                // Tables\Columns\IconColumn::make('template.is_catalogised')
                //     ->label('katalogizovana')
                //     ->boolean(),
                // Tables\Columns\TextColumn::make('template.people')
                //     ->label('pocet ludi'),
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
