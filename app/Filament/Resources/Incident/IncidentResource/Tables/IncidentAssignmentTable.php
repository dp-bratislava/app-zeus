<?php

namespace App\Filament\Resources\Incident\IncidentResource\Tables;

use App\Models\IncidentAssignment;
use App\States;
use Filament\Tables;
use Filament\Tables\Table;

class IncidentAssignmentTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->heading(__('incidents/incident.table.heading'))
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->recordClasses(fn($record) => match ($record->incident->state?->getValue()) {
                States\Incident\Created::$name => 'bg-blue-200',
                States\Incident\Closed::$name => 'bg-green-200',
                default => null,
            })
            ->columns([
                Tables\Columns\TextColumn::make('incident.date')
                    ->label(__('incidents/incident.table.columns.date'))
                    ->date(),
                Tables\Columns\TextColumn::make('subject.code.code')
                    ->label(__('incidents/incident.table.columns.subject')),
                // ->state(fn(Incident $record, IncidentAssignment $incidentAssignment) => $incidentAssignment->whereBelongsTo($record)->first()?->subject?->code?->code),
                Tables\Columns\TextColumn::make('incident.description')
                    ->label(__('incidents/incident.table.columns.description'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('state')
                    ->label(__('incidents/incident.table.columns.state'))
                    ->state(fn(IncidentAssignment $record) => $record->incident?->state?->label())
                    ->badge(),
                Tables\Columns\TextColumn::make('incident.type.title')
                    ->label(__('incidents/incident.table.columns.type'))
                    ->badge(),
            ])
            ->filters(IncidentTableFilters::make())
            ->headerActions([
                Tables\Actions\CreateAction::make()
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateRecordDataUsing(function (
                        $record,
                        array $data,
                        IncidentAssignment $incidentAssignment,
                    ): array {

                        $data['subject_id'] = $incidentAssignment->whereBelongsTo($record)->first()->subject->id;
                        dd($data);
                        return $data;
                    }),
                Tables\Actions\DeleteAction::make(),
                CreateTicketAction::make('create_ticket'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
