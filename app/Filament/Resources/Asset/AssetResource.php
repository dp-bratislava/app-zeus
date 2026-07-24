<?php

namespace App\Filament\Resources\Asset;

use App\Filament\Resources\Asset\AssetResource\Pages\ListAssets;
use App\Filament\Resources\Asset\AssetResource\Tables\AssetsTable;
use Dpb\Package\Assets\Models\Asset;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static ?string $recordTitleAttribute = 'serial_number';

    protected static ?string $slug = 'assets';

    public static function getModelLabel(): string
    {
        return 'Agregát';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Agregáty';
    }

    public static function getNavigationLabel(): string
    {
        return 'Agregáty';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Údržba';
    }

    public static function table(Table $table): Table
    {
        return AssetsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssets::route('/'),
        ];
    }
}
