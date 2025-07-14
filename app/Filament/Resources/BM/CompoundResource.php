<?php

namespace App\Filament\Resources\BM;

use App\Filament\Resources\BM\CompoundResource\Pages;
use App\Filament\Resources\BM\CompoundResource\RelationManagers;
use App\Models\BM\Compound;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompoundResource extends Resource
{
    protected static ?string $model = Compound::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Areály';
    protected static ?string $pluralModelLabel = 'Areály';
    protected static ?string $ModelLabel = 'Areál';

    public static function getNavigationGroup(): ?string
    {
        return 'Správa budov';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code'),
                Forms\Components\TextInput::make('title'),
                Forms\Components\TextInput::make('description'),
                TableRepeater::make('buildings')
                    ->relationship('buildings')
                    ->columnSpanFull()
                    ->headers([
                        Header::make('code'),
                        Header::make('title'),
                        Header::make('description'),
                    ])
                    ->schema([
                        Forms\Components\TextInput::make('code'),
                        Forms\Components\TextInput::make('title'),
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
            'index' => Pages\ListCompounds::route('/'),
            'create' => Pages\CreateCompound::route('/create'),
            'edit' => Pages\EditCompound::route('/{record}/edit'),
        ];
    }
}
