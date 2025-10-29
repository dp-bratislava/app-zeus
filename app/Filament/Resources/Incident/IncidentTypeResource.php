<?php

namespace App\Filament\Resources\Incident;

use App\Filament\Resources\Incident\IncidentTypeResource\Pages;
use Dpb\Package\Incidents\Models\IncidentType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class IncidentTypeResource extends Resource
{
    protected static ?string $model = IncidentType::class;

    public static function getModelLabel(): string
    {
        return __('incidents/incident-type.resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('incidents/incident-type.resource.plural_model_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('incidents/incident-type.navigation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('incidents/incident-type.navigation.group');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label(__('incidents/incident-type.form.fields.code.label')),
                Forms\Components\TextInput::make('title')
                    ->label(__('incidents/incident-type.form.fields.title.label')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->columns([
                TextColumn::make('code')->label(__('incidents/incident-type.table.columns.code.label')),
                TextColumn::make('title')->label(__('incidents/incident-type.table.columns.title.label')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
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
            'index' => Pages\ListIncidentTypes::route('/'),
            'create' => Pages\CreateIncidentType::route('/create'),
            'edit' => Pages\EditIncidentType::route('/{record}/edit'),
        ];
    }
}
