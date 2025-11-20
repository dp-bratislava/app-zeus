<?php

namespace App\Filament\Resources\Incident\IncidentResource\Tables;

use App\Models\IncidentAssignment;
use App\Models\TicketAssignment;
use App\Services\TS\TicketAssignmentService;
use App\States;
use Dpb\Package\Incidents\Models\Incident;
use Dpb\Package\Inspections\Models\Inspection;
use Filament\Tables;
use Filament\Tables\Table;

class IncidentTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->recordClasses(fn($record) => match ($record->state?->getValue()) {
                States\Incident\Created::$name => 'bg-blue-200',
                States\Incident\Closed::$name => 'bg-green-200',
                default => null,
            })
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label(__('incidents/incident.table.columns.date.label'))
                    ->date(),
                Tables\Columns\TextColumn::make('subject')
                    ->label(__('incidents/incident.table.columns.subject.label'))
                    ->state(fn(Incident $record, IncidentAssignment $incidentAssignment) => $incidentAssignment->whereBelongsTo($record)->first()?->subject?->code?->code),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('incidents/incident.table.columns.description.label')),
                Tables\Columns\TextColumn::make('state')
                    ->label(__('incidents/incident.table.columns.state.label'))
                    ->state(fn(Incident $record) => $record?->state?->label())
                    ->badge(),
                Tables\Columns\TextColumn::make('type.title')
                    ->label(__('incidents/incident.table.columns.type.label'))
                    ->badge(),
            ])
            ->filters([
                //
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
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
