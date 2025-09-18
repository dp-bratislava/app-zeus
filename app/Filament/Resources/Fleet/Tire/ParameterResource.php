<?php

namespace App\Filament\Resources\Fleet\Tire;

use App\Filament\Imports\Fleet\TireParameterImporter;
use App\Filament\Resources\Fleet\Tire\ParameterResource\Pages;
use App\Filament\Resources\Fleet\Tire\ParameterResource\RelationManagers;
use Dpb\Package\Fleet\Models\Tire\Parameter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ParameterResource extends Resource
{
    protected static ?string $model = Parameter::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Parametre pneumatik';
    protected static ?string $pluralModelLabel = 'Parametre pneumatik';
    protected static ?string $ModelLabel = 'Parametre pneumatik';

    public static function canViewAny(): bool
    {
        return false;
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Fleet';
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
                TextColumn::make('tire_width'),
                TextColumn::make('profile_number'),
                TextColumn::make('constructionType.title'),
                TextColumn::make('rim_diameter'),
                TextColumn::make('load_index'),
                TextColumn::make('speed_rating'),
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(TireParameterImporter::class)
                    ->csvDelimiter(';')
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
            'index' => Pages\ListParameters::route('/'),
            'create' => Pages\CreateParameter::route('/create'),
            'edit' => Pages\EditParameter::route('/{record}/edit'),
        ];
    }
}
