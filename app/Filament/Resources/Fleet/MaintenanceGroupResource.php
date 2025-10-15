<?php

namespace App\Filament\Resources\Fleet;

use App\Filament\Resources\Fleet\MaintenanceGroupResource\Pages;
use Dpb\Package\Fleet\Models\MaintenanceGroup;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MaintenanceGroupResource extends Resource
{
    protected static ?string $model = MaintenanceGroup::class;

    public static function getModelLabel(): string
    {
        return __('fleet/maintenance-group.resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('fleet/maintenance-group.resource.plural_model_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('fleet/maintenance-group.navigation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('fleet/maintenance-group.navigation.group');
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
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label(__('fleet/maintenance-group.table.columns.code')),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('fleet/maintenance-group.table.columns.title')),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('fleet/maintenance-group.table.columns.description')),
            ])
            ->filters([
                //
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
            'index' => Pages\ListMaintenanceGroups::route('/'),
            'create' => Pages\CreateMaintenanceGroup::route('/create'),
            'edit' => Pages\EditMaintenanceGroup::route('/{record}/edit'),
        ];
    }

    // public static function canCreate(): bool
    // {
    //     return auth()->check() && auth()->user()->can('fleet.maintenance-group.create');
    // }

    // public static function canEdit(Model $record): bool
    // {
    //     return auth()->check() && auth()->user()->can('fleet.maintenance-group.update');
    // }   
    
    // public static function canDelete(Model $record): bool
    // {        
    //     return auth()->check() && auth()->user()->can('fleet.maintenance-group.delete');
    // }    

}
