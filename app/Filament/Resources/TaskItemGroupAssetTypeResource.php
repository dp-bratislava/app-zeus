<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskItemGroupAssetTypeResource\Pages\EditTaskItemGroupAssetType;
use App\Filament\Resources\TaskItemGroupAssetTypeResource\Pages\ListTaskItemGroupAssetTypes;
use Dpb\Package\Assets\Models\AssetType;
use Dpb\Package\Tasks\Models\TaskItemGroup;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Lightweight mapping screen: assigns an asset type (device group) to a task item
 * group (podzákazka type). When set, the "zariadenia" tab appears on that podzákazka's
 * detail. The task item groups themselves are managed elsewhere; this resource only
 * edits the mapping.
 */
class TaskItemGroupAssetTypeResource extends Resource
{
    protected static ?string $model = TaskItemGroup::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLink;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getModelLabel(): string
    {
        return 'Mapovanie zariadení';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Mapovanie zariadení';
    }

    public static function getNavigationLabel(): string
    {
        return 'Podzákazky → zariadenia';
    }

    public static function getNavigationGroup(): ?string
    {
        return __('dpb-ast-ui::asset-type.navigation.group');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('code')
                ->label('Kód')
                ->disabled(),
            TextInput::make('title')
                ->label('Názov')
                ->disabled(),
            Select::make('asset_type_id')
                ->label('Typ zariadenia (zobrazí kartu „Zariadenia“)')
                ->options(fn (): array => AssetType::query()
                    ->orderBy('title')
                    ->pluck('title', 'id')
                    ->all())
                ->searchable()
                ->placeholder('— bez zariadení —'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kód')
                    ->searchable(),
                TextColumn::make('title')
                    ->label('Názov podzákazky')
                    ->searchable(),
                TextColumn::make('assetType.title')
                    ->label('Typ zariadenia')
                    ->badge()
                    ->placeholder('—'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTaskItemGroupAssetTypes::route('/'),
            'edit' => EditTaskItemGroupAssetType::route('/{record}/edit'),
        ];
    }
}
