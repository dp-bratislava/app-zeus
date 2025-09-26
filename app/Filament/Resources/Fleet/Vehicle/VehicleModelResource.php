<?php

namespace App\Filament\Resources\Fleet\Vehicle;

use App\Filament\Imports\Fleet\VehicleModelImporter;
use App\Filament\Resources\Fleet\Vehicle\VehicleModelResource\Pages;
use App\Filament\Resources\Fleet\Vehicle\VehicleModelResource\RelationManagers;
use Dpb\Package\Fleet\Models\VehicleModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VehicleModelResource extends Resource
{
    protected static ?string $model = VehicleModel::class;

    protected static ?string $navigationLabel = 'Modely vozidiel';
    protected static ?string $pluralModelLabel = 'Modely vozidiel';
    protected static ?string $ModelLabel = 'Model vozidla';

    public static function getNavigationGroup(): ?string
    {
        return 'Flotila';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('year'),
                TextColumn::make('seats'),
                TextColumn::make('fuel_consumption'),
                TextColumn::make('length')
                    ->state(function($record) {return $record->getAttrValue('length');}),
                TextColumn::make('warranty'),
                TextColumn::make('type.title'),
                TextColumn::make('fuelType.title'),
                TextColumn::make('seats')
                    ->state(function($record) {return $record->getAttrValue('seat-capacity');}),
                    // ->state(function($record) {return print_r($record->attributeValues());}),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(VehicleModelImporter::class)
                    ->csvDelimiter(';')
            ]) 
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehicleModels::route('/'),
            'create' => Pages\CreateVehicleModel::route('/create'),
            'edit' => Pages\EditVehicleModel::route('/{record}/edit'),
        ];
    }
}
