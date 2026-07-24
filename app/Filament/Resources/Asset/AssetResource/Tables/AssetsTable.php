<?php

namespace App\Filament\Resources\Asset\AssetResource\Tables;

use Dpb\WtfTmsBridge\Enums\AssetState;
use Dpb\WtfTmsBridge\Enums\MovementType;
use Dpb\Package\Assets\Contracts\AssetStateInterface;
use Dpb\Package\Assets\Contracts\MovementTypeInterface;
use Dpb\Package\Assets\Models\Asset;
use Dpb\Package\Assets\Models\AssetMovement;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Dpb\Package\TaskMS\Models\TaskAssignment;
use Dpb\Package\Fleet\Models\Vehicle;

class AssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Agregáty')
            ->emptyStateHeading('Žiadne agregáty')
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->columns([
                TextColumn::make('serial_number')
                    ->label('Sériové číslo')
                    ->searchable(isIndividual: true)
                    ->width('400px')
                    ->sortable(),

                TextColumn::make('type.title')
                    ->label('Typ')
                    ->searchable(isIndividual: true)
                    ->width('200px')
                    ->sortable(),

                TextColumn::make('state_location')
                    ->label('Umiestnenie')
                    ->getStateUsing(fn ($record) => $record->state)
                    ->formatStateUsing(fn (AssetStateInterface $state): string => $state->location())
                    ->width('200px'),

                TextColumn::make('kilometrage')
                    ->label('Km')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                Textcolumn::make('last_movement')
                    ->label('Posledná operácia')
                    ->getStateUsing(fn (Asset $record) => $record->movements()->latest()->first()?->movement_type ?? null)
                    ->formatStateUsing(fn (?MovementTypeInterface $state): string => $state?->label() ?? '')
                    ->width('100px')
                    ->sortable(),

                Textcolumn::make('last_movement_vehicle')
                    ->label('Posledné vozidlo')
                    ->getStateUsing(function (Asset $record) { 
                        $latestMovement = $record->movements()
                            ->with('taskItem.task')
                            ->latest()
                            ->first();

                        $task = $latestMovement?->taskItem?->task;
                        if (!$task) {
                            return '';
                        }

                        $assignment = TaskAssignment::where('task_id', $task->id)
                            ->with('subject')
                            ->first();

                        if (!$assignment || !$assignment->subject) {
                            return '';
                        }

                        if ($assignment->subject instanceof Vehicle) {
                            return $assignment->subject->label ?? '';
                        }

                        return $assignment->subject->label ?? '';
                    })
                    ->width('100px'),

                Textcolumn::make('last_task_item')
                    ->label('Posledná podzákazka')
                    ->getStateUsing(function (Asset $record) {
                        return $record->latestMovement?->taskItem?->id;
                    })
                    ->color('primary')
                    ->url(function (Asset $record) {
                        $movement = $record->movements()->with('taskItem.task')->latest()->first();
                        $task = $movement?->taskItem?->task;
                        if (!$task) return null;

                        $assignment = TaskAssignment::where('task_id', $task->id)->first();
                        if (!$assignment) return null;

                        $taskItemId = $movement->taskItem->id;

                        return \Dpb\WtfTmsBridge\Filament\Resources\Task\TaskAssignmentResource::getUrl('edit', [
                            'record' => $assignment,
                        ]) . '?taskItemId=' . $taskItemId;
                    })
                    ->openUrlInNewTab()
                    ->width('100px')
                    ->sortable(),

                TextColumn::make('state')
                    ->label('Stav')
                    ->width('200px')
                    ->badge()
                    ->formatStateUsing(fn (AssetStateInterface $state): string => $state->label())
                    ->color('warning'),
            ])
            ->filters([])
            ->defaultSort('updated_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->whereNotInState([
                AssetState::DIEL_NA_VOZE,
                AssetState::VYRADENE_SCHVALENE,
            ]));
    }
}
