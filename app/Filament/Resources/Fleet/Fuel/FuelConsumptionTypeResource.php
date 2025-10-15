<?php

namespace App\Filament\Resources\Fleet\Fuel;

use App\Filament\Resources\Fleet\Fuel\FuelConsumptionTypeResource\Pages;
use App\Filament\Resources\Fleet\Fuel\FuelConsumptionTypeResource\RelationManagers;
use Dpb\Package\Fleet\Models\FuelConsumptionType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FuelConsumptionTypeResource extends Resource
{
    protected static ?string $model = FuelConsumptionType::class;
    public static function canViewAny(): bool
    {
        return false;
    }
    public static function getNavigationGroup(): ?string
    {
        return 'Flotila';
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
            'index' => Pages\ListFuelConsumptionTypes::route('/'),
            'create' => Pages\CreateFuelConsumptionType::route('/create'),
            'edit' => Pages\EditFuelConsumptionType::route('/{record}/edit'),
        ];
    }
}
