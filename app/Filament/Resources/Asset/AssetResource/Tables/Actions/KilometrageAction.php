<?php

namespace App\Filament\Resources\Asset\AssetResource\Tables\Actions;

use Filament\Actions\Action;
use Dpb\WtfTmsBridge\Models\Asset;
use App\Filament\Resources\Asset\AssetResource\Tables\Schemas\KilometrageDetailSchema;

final class KilometrageAction
{
    public static function make(): Action
    {     
        return Action::make('kmDetail')
                ->modalHeading(fn (Asset $record) => 'Kilometráž – '.$record->serial_number)
                ->modalDescription('Rozpis kilometrov podľa vozidiel, na ktorých bol agregát namontovaný.')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Zavrieť')
                ->modalWidth('2xl')
                ->schema(fn (Asset $record): array => KilometrageDetailSchema::kilometrageDetailSchema($record));
    }
}
