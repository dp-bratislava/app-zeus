<?php

namespace App\Filament\Resources\BM;

use App\Filament\Resources\BM\OfficeResource\Pages;
use App\Filament\Resources\BM\OfficeResource\RelationManagers;
use App\Models\BM\Office;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OfficeResource extends Resource
{
    protected static ?string $model = Office::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Miestnosti';
    protected static ?string $pluralModelLabel = 'Miestnosti';
    protected static ?string $ModelLabel = 'Miestnosť';

    public static function getNavigationGroup(): ?string
    {
        return 'Správa budov';
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
                Tables\Columns\TextColumn::make('code'),
                Tables\Columns\TextColumn::make('description'),
                Tables\Columns\TextColumn::make('floor'),
                Tables\Columns\TextColumn::make('building.title'),
                Tables\Columns\TextColumn::make('building.compound.title'),
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
            'index' => Pages\ListOffices::route('/'),
            'create' => Pages\CreateOffice::route('/create'),
            'edit' => Pages\EditOffice::route('/{record}/edit'),
        ];
    }
}
