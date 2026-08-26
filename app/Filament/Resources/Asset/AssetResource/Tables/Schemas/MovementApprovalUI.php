<?php

namespace App\Filament\Resources\Asset\AssetResource\Tables\Schemas;

use Filament\Actions\BulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Collection;
use Dpb\WtfTmsBridge\Models\Asset;
use Filament\Support\Enums\Alignment;
use App\Filament\Resources\Asset\AssetResource\Tables\Services\MovementApprovalService;


final class MovementApprovalUI
{
    public static function approveBulkAction(): BulkAction
    {
        return BulkAction::make('approveMovements')
            ->label('Schváliť operácie')
            ->icon('heroicon-o-check-badge')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Schváliť vybrané operácie')
            ->modalDescription('Budú schválené len agregáty, ktoré vyžadujú schválenie poslednej operácie.')
            ->schema(fn (Collection $records): array => self::movementInfoSchema($records))
            ->action(fn (Collection $records) => MovementApprovalService::approveMovements($records))
            ->modalWidth('full')
            ->closeModalByClickingAway(false);
    }

    public static function rejectBulkAction(): BulkAction
    {
        return BulkAction::make('rejectMovements')
            ->label('Zamietnuť operácie')
            ->icon('heroicon-o-x-mark')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Zamietnuť vybrané operácie')
            ->modalDescription('Budú zamietnuté len agregáty, ktoré vyžadujú schválenie poslednej operácie.')
            ->schema(fn (Collection $records): array => self::movementInfoSchema($records))
            ->action(fn (Collection $records) => MovementApprovalService::rejectMovements($records))
            ->modalWidth('full')
            ->closeModalByClickingAway(false);
    }

    public static function postponeBulkAction(): BulkAction
    {
        return BulkAction::make('postponeMovements')
            ->label('Vrátiť do schvalovania')
            ->icon('heroicon-o-clock')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading('Vrátiť do schvalovania')
            ->modalDescription('Budú odložené len agregáty, ktoré vyžadujú schválenie poslednej operácie.')
            ->schema(fn (Collection $records): array => self::movementInfoSchema($records))
            ->action(fn (Collection $records) => MovementApprovalService::postponeMovements($records))
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
                'approval_status' => $movement?->getApprovalStatus()?->label() ?? '—',
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
                    TableColumn::make('Stav'),
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
}