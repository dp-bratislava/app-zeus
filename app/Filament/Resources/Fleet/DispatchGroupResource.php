<?php

namespace App\Filament\Resources\Fleet;

use App\Filament\Resources\Fleet\DispatchGroupResource\Pages;
use Dpb\Package\Fleet\Models\DispatchGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DispatchGroupResource extends Resource
{
    protected static ?string $model = DispatchGroup::class;

    public static function getModelLabel(): string
    {
        return __('fleet/dispatch-group.resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('fleet/dispatch-group.resource.plural_model_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('fleet/dispatch-group.navigation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('fleet/dispatch-group.navigation.group');
    }

    public static function canViewAny(): bool
    {
        return false;
    }
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label(__('fleet/dispatch-group.form.fields.code.label')),
                Forms\Components\TextInput::make('title')
                    ->label(__('fleet/dispatch-group.form.fields.title')),
                Forms\Components\TextInput::make('description')
                    ->label(__('fleet/dispatch-group.form.fields.description')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label(__('fleet/dispatch-group.table.columns.code')),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('fleet/dispatch-group.table.columns.title')),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('fleet/dispatch-group.table.columns.description')),
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
            'index' => Pages\ListDispatchGroups::route('/'),
            'create' => Pages\CreateDispatchGroup::route('/create'),
            'edit' => Pages\EditDispatchGroup::route('/{record}/edit'),
        ];
    }

    // public static function canCreate(): bool
    // {
    //     return auth()->check() && auth()->user()->can('fleet.dispatch-group.create');
    // }

    // public static function canEdit(Model $record): bool
    // {
    //     return auth()->check() && auth()->user()->can('fleet.dispatch-group.update');
    // }   
    
    // public static function canDelete(Model $record): bool
    // {        
    //     return auth()->check() && auth()->user()->can('fleet.dispatch-group.delete');
    // }        
}
