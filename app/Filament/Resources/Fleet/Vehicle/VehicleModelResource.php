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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VehicleModelResource extends Resource
{
    protected static ?string $model = VehicleModel::class;

    public static function getModelLabel(): string
    {
        return __('fleet/vehicle-model.resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('fleet/vehicle-model.resource.plural_model_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('fleet/vehicle-model.navigation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('fleet/vehicle-model.navigation.group');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label(__('fleet/vehicle-model.form.fields.title.label')),
                Forms\Components\TextInput::make('year')
                    ->label(__('fleet/vehicle-model.form.fields.year.label'))
                    ->numeric(),

                Forms\Components\Select::make('type_id')                
                    ->label(__('fleet/vehicle-model.form.fields.type.label'))
                    ->relationship('type', 'title')
                    ->searchable()
                    ->preload()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->columns([
                TextColumn::make('title')
                    ->label(__('fleet/vehicle-model.table.columns.title.label')),
                TextColumn::make('year')
                    ->label(__('fleet/vehicle-model.table.columns.year.label')),
                TextColumn::make('length')
                    ->label(__('fleet/vehicle-model.table.columns.length.label'))
                    ->state(function ($record) {
                        // dd($record->attributeValues());
                        return $record->getAttrValue('length');
                    })
                    ->numeric(decimalPlaces: 2),

                // TextColumn::make('seats'),
                // TextColumn::make('fuel_consumption'),
                // TextColumn::make('length')
                //     ->state(function ($record) {
                //         return $record->getAttrValue('length');
                //     }),
                // TextColumn::make('warranty'),
                TextColumn::make('type.title')
                    ->label(__('fleet/vehicle-model.table.columns.type.label')),
                // TextColumn::make('fuelType.title'),
                // TextColumn::make('seats')
                //     ->state(function ($record) {
                //         return $record->getAttrValue('seat-capacity');
                //     }),
                // ->state(function($record) {return print_r($record->attributeValues());}),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(VehicleModelImporter::class)
                    ->csvDelimiter(';')
                    ->visible(auth()->user()->can('fleet.vehicle-model.import'))
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(auth()->user()->can('fleet.vehicle-model.update')),
                Tables\Actions\DeleteAction::make()
                    ->visible(auth()->user()->can('fleet.vehicle-model.delete')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(auth()->user()->can('fleet.vehicle-model.bulk-delete')),
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

    public static function canCreate(): bool
    {
        return auth()->check() && auth()->user()->can('fleet.vehicle-model.create');
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->check() && auth()->user()->can('fleet.vehicle-model.update');
    }   
    
    public static function canDelete(Model $record): bool
    {        
        return auth()->check() && auth()->user()->can('fleet.vehicle-model.delete');
    }  

}
