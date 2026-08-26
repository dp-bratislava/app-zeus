<?php

namespace App\Filament\Resources\Asset\AssetResource\Tables\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\TextInput;
use Dpb\WtfTmsBridge\Models\Asset;
use Filament\Support\Enums\Alignment;

final class KilometrageDetailSchema
{
    public static function kilometrageDetailSchema(Asset $record): array
    {
        $rows = collect($record->getKilometrageByVehicle())
            ->map(fn (array $row) => [
                'vehicle' => $row['label'],
                'date' => $row['date'] ? $row['date']->format('Y-m-d') : null,
                'km' => number_format($row['km'], 0, ',', ' ') . ' km',

            ])
            ->values()
            ->all();
        return [
            Repeater::make('vehicles')
                ->hiddenLabel()
                ->table([
                    TableColumn::make('Vozidlo')
                        ->alignment(Alignment::Start),
                    TableColumn::make('Najazdené kilometre')
                        ->alignment(Alignment::Center),
                    TableColumn::make('Ku dňu')
                        ->alignment(Alignment::Start),
                ])
                ->schema([
                    TextInput::make('vehicle')
                        ->disabled()
                        ->dehydrated(),
                    TextInput::make('km')
                        ->disabled()
                        ->dehydrated(),
                    DatePicker::make('date')
                        ->disabled()
                        ->dehydrated(),
                ])
                ->columns(3)
                ->columnSpanFull()
                ->default($rows)
                ->addable(false)
                ->deletable(false)
                ->reorderable(false)
                ->disabled(),
        ];
    }
}
