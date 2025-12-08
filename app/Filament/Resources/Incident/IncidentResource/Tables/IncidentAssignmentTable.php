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
            ->description(__('incidents/incident.table.description'))
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->recordClasses(fn($record) => match ($record->incident->state?->getValue()) {
                States\Incident\Created::$name => 'bg-blue-200',
                States\Incident\Closed::$name => 'bg-green-200',
                default => null,
            })
            ->columns([
                // date
                Tables\Columns\TextColumn::make('incident.date')
                    ->label(__('incidents/incident.table.columns.date'))
                    ->date('j.n.Y'),
                // type
                Tables\Columns\TextColumn::make('incident.type.title')
                    ->label(__('incidents/incident.table.columns.type'))
                    ->badge(),
                // subject
                Tables\Columns\TextColumn::make('subject.code.code')
                    ->label(__('incidents/incident.table.columns.subject')),
                // ->state(fn(Incident $record, IncidentAssignment $incidentAssignment) => $incidentAssignment->whereBelongsTo($record)->first()?->subject?->code?->code),
                Tables\Columns\TextColumn::make('state')
                    ->label(__('incidents/incident.table.columns.state'))
                    ->state(fn(IncidentAssignment $record) => $record->incident?->state?->label())
                    ->badge(),
                // description
                Tables\Columns\TextColumn::make('incident.description')
                    ->label(__('incidents/incident.table.columns.description'))
                    ->grow()
                    ->searchable(),
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
                // CreateTaskAction::make('create_task'),
                CreateTicketAction::make('create_ticket'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
