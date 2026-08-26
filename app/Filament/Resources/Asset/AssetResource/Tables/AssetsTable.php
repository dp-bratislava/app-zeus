<?php

namespace App\Filament\Resources\Asset\AssetResource\Tables;

use Dpb\Package\Assets\Contracts\AssetStateInterface;
use Dpb\Package\Assets\Contracts\MovementTypeInterface;
use Dpb\Package\Assets\Enums\ApprovalStatus;
use Dpb\WtfTmsBridge\Models\Asset;
use Dpb\Package\TaskMS\Models\TaskAssignment;
use Dpb\WtfTmsBridge\Filament\Resources\Task\TaskAssignmentResource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Filament\Resources\Asset\AssetResource\Tables\Actions\KilometrageAction;

class AssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('')
            ->emptyStateHeading('Žiadne agregáty')
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->columns([
                TextColumn::make('serial_number')
                    ->label('Sériové číslo')
                    ->width('400px')
                    ->color(fn (Asset $record): ?string => $record->hasVirtualSerialNumber() ? 'danger' : null)
                    ->tooltip(fn (Asset $record): ?string => $record->hasVirtualSerialNumber() ? 'Virtuálne sériové číslo' : null),

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

                TextColumn::make('calculated_kilometrage')
                    ->label('Km')
                    ->sortable()
                    ->numeric(thousandsSeparator: ' ',)
                    ->color('primary')
                    ->action(KilometrageAction::make()),
                TextColumn::make('last_movement')
                    ->label('Posledná operácia')
                    ->getStateUsing(fn (Asset $record) => $record->latestMovement->movement_type ?? null)
                    ->formatStateUsing(fn (?MovementTypeInterface $state): string => $state?->label() ?? '')
                    ->width('100px')
                    ->sortable(),

                TextColumn::make('last_movement_date')
                    ->label('Dátum poslednej operácie')
                    ->getStateUsing(fn (Asset $record) => $record->latestMovement?->updated_at ?? null)
                    ->date('d.m.Y')
                    ->width('100px')
                    ->sortable(),                    

                TextColumn::make('last_movement_vehicle')
                    ->label('Vozidlo')
                    ->getStateUsing(function (Asset $record) {
                        $vehicle = $record->latestMovement?->vehicle;
                        return $vehicle->label ?? '';
                    })
                    ->width('100px'),

                TextColumn::make('last_task_item')
                    ->label('Posledná podzákazka')
                    ->getStateUsing(function (Asset $record) {
                        return $record->latestMovement?->taskItem?->id;
                    })
                    ->color('primary')
                    ->url(function (Asset $record) {
                        $movement = $record->latestMovement;
                        $task = $movement?->taskItem?->task;
                        if (! $task) {
                            return null;
                        }

                        $assignment = TaskAssignment::where('task_id', $task->id)->first();
                        if (! $assignment) {
                            return null;
                        }

                        $taskItemId = $movement->taskItem->id;

                        return TaskAssignmentResource::getUrl('edit', [
                            'record' => $assignment,
                        ]).'?taskItemId='.$taskItemId;
                    })
                    ->openUrlInNewTab()
                    ->width('50px')
                    ->sortable(),

                TextColumn::make('state')
                ->label('Stav')
                ->width('200px')
                ->badge()
                ->formatStateUsing(function (AssetStateInterface $state, Asset $record): string {
                    $label = $state->label();
                    
                    if ($record->latestMovement?->getApprovalStatus() === ApprovalStatus::PENDING) {
                        $label .= ' (' . ApprovalStatus::PENDING->label() . ')';
                    }
                    
                    return $label;
                })
                ->color(function (Asset $record): string {
                    $status = $record->latestMovement?->getApprovalStatus();
                    return match ($status) {
                        ApprovalStatus::PENDING => 'warning',
                        ApprovalStatus::APPROVED => 'success',
                        ApprovalStatus::REJECTED => 'danger',
                        ApprovalStatus::NOT_REQUIRED => 'success',
                        default => 'primary',
                    };
                }),
            ])
            ->filters([])
            ->defaultSort('updated_at', 'desc');
    }
}

