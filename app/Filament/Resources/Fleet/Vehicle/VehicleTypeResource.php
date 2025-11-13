<?php

namespace App\Filament\Resources\Fleet\Vehicle;

use App\Filament\Imports\Fleet\VehicleTypeImporter;
use App\Filament\Resources\Fleet\Vehicle\VehicleTypeResource\Pages;
use Dpb\Package\Fleet\Models\VehicleType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VehicleTypeResource extends Resource
{
    protected static ?string $model = VehicleType::class;

    public static function getModelLabel(): string
    {
        return __('fleet/vehicle-type.resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('fleet/vehicle-type.resource.plural_model_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('fleet/vehicle-type.navigation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('fleet/vehicle-type.navigation.group');
    }

    public static function getNavigationSort(): ?int
    {
        return config('pkg-fleet.navigation.vehicle-type') ?? 999;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label(__('fleet/vehicle-type.form.fields.code.label')),
                Forms\Components\TextInput::make('title')
                    ->label(__('fleet/vehicle-type.form.fields.title')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->columns([
                TextColumn::make('code')->label(__('fleet/vehicle-type.table.columns.code.label')),
                TextColumn::make('title')->label(__('fleet/vehicle-type.table.columns.title.label')),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(VehicleTypeImporter::class)
                    ->csvDelimiter(';')
                // ->visible(auth()->user()->can('fleet.vehicle-type.import'))
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // ->visible(auth()->user()->can('fleet.vehicle-type.update')),
                Tables\Actions\DeleteAction::make()
                // ->visible(auth()->user()->can('fleet.vehicle-type.delete')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                    // ->visible(auth()->user()->can('fleet.vehicle-type.bulk-delete')),
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
            'index' => Pages\ListVehicleTypes::route('/'),
            'create' => Pages\CreateVehicleType::route('/create'),
            'edit' => Pages\EditVehicleType::route('/{record}/edit'),
        ];
    }

    // public static function canCreate(): bool
    // {
    //     return auth()->check() && auth()->user()->can('fleet.vehicle-type.create');
    // }

    // public static function canEdit(Model $record): bool
    // {
    //     return auth()->check() && auth()->user()->can('fleet.vehicle-type.update');
    // }   

    // public static function canDelete(Model $record): bool
    // {        
    //     return auth()->check() && auth()->user()->can('fleet.vehicle-type.delete');
    // }    
}
