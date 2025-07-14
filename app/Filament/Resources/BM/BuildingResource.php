<?php

namespace App\Filament\Resources\BM;

use App\Filament\Resources\BM\BuildingResource\Pages;
use App\Filament\Resources\BM\BuildingResource\RelationManagers;
use App\Models\BM\Building;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BuildingResource extends Resource
{
    protected static ?string $model = Building::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Budovy';
    protected static ?string $pluralModelLabel = 'Budovy';
    protected static ?string $ModelLabel = 'Budova';

    public static function getNavigationGroup(): ?string
    {
        return 'Správa budov';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('compound_id')
                    ->relationship('compound', 'title'),
                Forms\Components\TextInput::make('code'),
                Forms\Components\TextInput::make('title'),
                Forms\Components\TextInput::make('description'),
                TableRepeater::make('offices')
                    ->relationship('offices')
                    ->columnSpanFull()
                    ->headers([
                        Header::make('code'),
                        Header::make('floor'),
                        Header::make('description'),
                    ])
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->required(),
                        Forms\Components\TextInput::make('floor')
                            ->integer(),
                        Forms\Components\TextInput::make('description'),

                    ])
                    ->cloneable()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->columns([
                Tables\Columns\TextColumn::make('code'),
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('description'),
                Tables\Columns\TextColumn::make('compound.title'),
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
            'index' => Pages\ListBuildings::route('/'),
            'create' => Pages\CreateBuilding::route('/create'),
            'edit' => Pages\EditBuilding::route('/{record}/edit'),
        ];
    }
}
