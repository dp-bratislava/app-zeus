<?php

namespace App\Filament\Resources\Fleet\Vehicle;

use App\Filament\Imports\Fleet\VehicleGroupImporter;
use App\Filament\Resources\Fleet\Vehicle\VehicleGroupResource\Pages;
use App\Filament\Resources\Fleet\Vehicle\VehicleGroupResource\RelationManagers;
use Dpb\Package\Fleet\Models\VehicleGroup;
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

class VehicleGroupResource extends Resource
{
    protected static ?string $model = VehicleGroup::class;

    public static function getModelLabel(): string
    {
        return __('fleet/vehicle-group.resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('fleet/vehicle-group.resource.plural_model_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('fleet/vehicle-group.navigation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('fleet/vehicle-group.navigation.group');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label(__('fleet/vehicle-group.form.fields.code.label')),
                Forms\Components\TextInput::make('title')
                    ->label(__('fleet/vehicle-group.form.fields.title')),
                Forms\Components\TextInput::make('description')
                    ->label(__('fleet/vehicle-group.form.fields.description')),                    
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->columns([
                TextColumn::make('code')->label(__('fleet/vehicle-group.table.columns.code.label')),
                TextColumn::make('title')->label(__('fleet/vehicle-group.table.columns.title.label')),
                TextColumn::make('description')->label(__('fleet/vehicle-group.table.columns.description.label')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(VehicleGroupImporter::class)
                    ->csvDelimiter(';')
                    // ->visible(auth()->user()->can('fleet.vehicle-group.import'))
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                    // ->visible(auth()->user()->can('fleet.vehicle-group.update')),
                Tables\Actions\DeleteAction::make()
                    // ->visible(auth()->user()->can('fleet.vehicle-group.delete')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        // ->visible(auth()->user()->can('fleet.vehicle-group.bulk-delete')),
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
            'index' => Pages\ListVehicleGroups::route('/'),
            'create' => Pages\CreateVehicleGroup::route('/create'),
            'edit' => Pages\EditVehicleGroup::route('/{record}/edit'),
        ];
    }

    // public static function canCreate(): bool
    // {
    //     return auth()->check() && auth()->user()->can('fleet.vehicle-group.create');
    // }

    // public static function canEdit(Model $record): bool
    // {
    //     return auth()->check() && auth()->user()->can('fleet.vehicle-group.update');
    // }   
    
    // public static function canDelete(Model $record): bool
    // {        
    //     return auth()->check() && auth()->user()->can('fleet.vehicle-group.delete');
    // }       
}
