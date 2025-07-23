<?php

namespace App\Filament\Resources\Fleet\Tire;

use App\Filament\Resources\Fleet\Tire\TireResource\Pages;
use App\Filament\Resources\Fleet\Tire\TireResource\RelationManagers;
use App\Models\Fleet\Tire\Tire;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TireResource extends Resource
{
    protected static ?string $model = Tire::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                //
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
            'index' => Pages\ListTires::route('/'),
            'create' => Pages\CreateTire::route('/create'),
            'edit' => Pages\EditTire::route('/{record}/edit'),
        ];
    }
}
