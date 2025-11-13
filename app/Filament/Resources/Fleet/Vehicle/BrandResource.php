<?php

namespace App\Filament\Resources\Fleet\Vehicle;

use App\Filament\Resources\Fleet\Vehicle\BrandResource\Forms\BrandForm;
use App\Filament\Resources\Fleet\Vehicle\BrandResource\Pages;
use App\Filament\Resources\Fleet\Vehicle\BrandResource\Tables\BrandTable;
use Dpb\Package\Fleet\Models\Brand;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    public static function getModelLabel(): string
    {
        return __('fleet/brand.resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('fleet/brand.resource.plural_model_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('fleet/brand.navigation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('fleet/brand.navigation.group');
    }

    public static function getNavigationSort(): ?int
    {
        return config('pkg-fleet.navigation.brand') ?? 999;
    }

    public static function form(Form $form): Form
    {
        return BrandForm::make($form);
    }

    public static function table(Table $table): Table
    {
        return BrandTable::make($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBrands::route('/'),
            'create' => Pages\CreateBrand::route('/create'),
            'edit' => Pages\EditBrand::route('/{record}/edit'),
        ];
    }
}
