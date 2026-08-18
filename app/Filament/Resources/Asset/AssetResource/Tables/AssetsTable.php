<?php

namespace App\Filament\Resources\Asset\AssetResource\Tables;

use Dpb\Package\Assets\Contracts\AssetStateInterface;
use Dpb\Package\Assets\Contracts\MovementTypeInterface;
use Dpb\Package\Assets\Enums\ApprovalStatus;
use Dpb\WtfTmsBridge\Models\Asset;
use Dpb\Package\TaskMS\Models\TaskAssignment;
use Dpb\WtfTmsBridge\Filament\Resources\Task\TaskAssignmentResource;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Dpb\DpbUtils\Helpers\UserPermissionHelper;
use Filament\Support\RawJs;

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

                TextColumn::make('calculated_kilometrage')
                    ->label('Km')
                    ->sortable()
                    ->numeric(
                        thousandsSeparator: ' ',
                    )
                    ->color('primary')
                    ->action(
                        Action::make('kmDetail')
                            ->modalHeading(fn (Asset $record) => 'Kilometráž – '.$record->serial_number)
                            ->modalDescription('Rozpis kilometrov podľa vozidiel, na ktorých bol agregát namontovaný.')
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Zavrieť')
                            ->modalWidth('2xl')
                            ->schema(fn (Asset $record): array => self::kilometrageDetailSchema($record))
                    ),
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
                        $latestMovement = $record->latestMovement;

                        $task = $latestMovement?->taskItem?->task;
                        if (! $task) {
                            return '';
                        }

                        $assignment = TaskAssignment::where('task_id', $task->id)
                            ->with('subject')
                            ->first();

                        if (! $assignment || ! $assignment->subject) {
                            return '';
                        }

                        return $assignment->subject->label ?? '';
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
                    
                    if ($record->latestMovement?->approval_status === ApprovalStatus::PENDING) {
                        $label .= ' (' . ApprovalStatus::PENDING->label() . ')';
                    }
                    
                    return $label;
                })
                ->color(fn (Asset $record): string => 
                    $record->latestMovement?->approval_status === ApprovalStatus::PENDING ? 'warning' : 'info'
                ),
            ])
            ->filters([])
            ->defaultSort('updated_at', 'desc');
    }

    public static function approveBulkAction(): BulkAction
    {
        return BulkAction::make('approveMovements')
            ->label('Schváliť pohyby')
            ->icon('heroicon-o-check-badge')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Schváliť vybrané pohyby')
            ->modalDescription('Budú schválené len agregáty, ktoré vyžadujú schválenie poslednej operácie.')
            ->schema(fn (Collection $records): array => self::movementInfoSchema($records))
            ->action(fn (Collection $records) => self::approveMovements($records))
            ->modalWidth('full')
            ->closeModalByClickingAway(false);
    }

    public static function rejectBulkAction(): BulkAction
    {
        return BulkAction::make('rejectMovements')
            ->label('Zamietnuť pohyby')
            ->icon('heroicon-o-x-mark')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Zamietnuť vybrané pohyby')
            ->modalDescription('Budú zamietnuté len agregáty, ktoré vyžadujú schválenie poslednej operácie.')
            ->schema(fn (Collection $records): array => self::movementInfoSchema($records))
            ->action(fn (Collection $records) => self::rejectMovements($records))
            ->modalWidth('full')
            ->closeModalByClickingAway(false);
    }

    protected static function movementInfoSchema(Collection $records): array
    {
        $rows = [];
        foreach ($records as $asset) {
            if (! $asset instanceof Asset) {
                continue;
            }

            $movement = $asset->latestMovement;

            $rows[] = [
                'serial_number' => $asset->serial_number,
                'movement_type' => $movement?->movement_type?->label() ?? '—',
                'date' => $movement?->date?->format('Y-m-d'),
                'state_result' => $movement?->state_result?->label() ?? '—',
                'approval_status' => $movement?->approval_status?->label() ?? '—',
            ];
        }

        return [
            Repeater::make('movements')
                ->hiddenLabel()
                ->table([
                    TableColumn::make('Sériové číslo')
                        ->alignment(Alignment::Start)
                        ->markAsRequired(),
                    TableColumn::make('Typ pohybu'),
                    TableColumn::make('Dátum'),
                    TableColumn::make('Stav po schválení'),
                    TableColumn::make('Stav schválenia'),
                ])
                ->schema(self::movementRowSchema())
                ->columns(8)
                ->columnSpanFull()
                ->default($rows)
                ->addable(false)
                ->deletable(false)
                ->reorderable(false)
                ->disabled(),
        ];
    }

    private static function movementRowSchema(): array
    {
        return [
            TextInput::make('serial_number')
                ->disabled()
                ->dehydrated(),
            TextInput::make('movement_type')
                ->disabled()
                ->dehydrated(),
            DatePicker::make('date')
                ->disabled()
                ->dehydrated(),
            TextInput::make('state_result')
                ->disabled()
                ->dehydrated(),
            TextInput::make('approval_status')
                ->disabled()
                ->dehydrated(),
        ];
    }

    protected static function kilometrageDetailSchema(Asset $record): array
    {
        $rows = collect($record->getKilometrageByVehicle())
            ->map(fn (array $row) => [
                'vehicle' => $row['label'],
                'km' => number_format($row['km'], 0, ',', ' '),
            ])
            ->values()
            ->all();

        return [
            Repeater::make('vehicles')
                ->hiddenLabel()
                ->table([
                    TableColumn::make('Vozidlo')
                        ->alignment(Alignment::Start),
                    TableColumn::make('km')
                        ->alignment(Alignment::End),
                ])
                ->schema([
                    TextInput::make('vehicle')
                        ->disabled()
                        ->dehydrated(),
                    TextInput::make('km')
                        ->disabled()
                        ->dehydrated(),
                ])
                ->columns(2)
                ->columnSpanFull()
                ->default($rows)
                ->addable(false)
                ->deletable(false)
                ->reorderable(false)
                ->disabled(),
        ];
    }

    public static function approveMovements(Collection $records): void
    {
        self::checkIfCanApprove();
        [$approvedCount, $rejectedCount] = self::processMovements($records, ApprovalStatus::APPROVED);

        if ($rejectedCount > 0) {
            Notification::make()
                ->title('Neschválené (nie je potrebné / už schválené): '.$rejectedCount)
                ->warning()
                ->send();
        }

        Notification::make()
            ->title('Schválené pohyby: '.$approvedCount)
            ->success()
            ->send();
    }

    public static function rejectMovements(Collection $records): void
    {
        self::checkIfCanApprove();
        [$processedApproved, $processedRejected] = self::processMovements($records, ApprovalStatus::REJECTED);

        if ($processedApproved > 0) {
            Notification::make()
                ->title('Schválené (nie je potrebné / už schválené): '.$processedApproved)
                ->success()
                ->send();
        }

        Notification::make()
            ->title('Zamietnuté pohyby: '.$processedRejected)
            ->danger()
            ->send();
    }

    protected static function processMovements(Collection $records, ApprovalStatus $targetStatus): array
    {
        $processedApproved = 0;
        $processedRejected = 0;

        foreach ($records as $asset) {
            
            if (! $asset instanceof Asset || ! $asset->waitingForApproval()) {
                continue;
            }

            $movement = $asset->latestMovement;

            
            if (! $movement || $movement->approval_status === ApprovalStatus::APPROVED) {
                continue;
            }

            $movement->update([
                'approval_status' => $targetStatus,
                'approved_by' => Auth::user()->id,
                'approved_at' => now(),
            ]);

            if ($targetStatus === ApprovalStatus::REJECTED) {
                $processedRejected++;
            } else {
                $processedApproved++;
            }
        }

        return [$processedApproved, $processedRejected];
    }

    protected static function checkIfCanApprove(): void
    {
        abort_unless(
            UserPermissionHelper::hasPermission('pkg-assets.asset-movement.approve'),
            403,
        );
    }

}

