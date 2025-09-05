<?php

namespace App\Filament\Resources\Fleet\Tire;

use App\Filament\Resources\Fleet\Tire\ConstructionTypeResource\Pages;
use App\Filament\Resources\Fleet\Tire\ConstructionTypeResource\RelationManagers;
use Dpb\Packages\Vehicles\Models\Tire\ConstructionType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ConstructionTypeResource extends Resource
{
    protected static ?string $model = ConstructionType::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                TextColumn::make('code'),
                TextColumn::make('title'),
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
            'index' => Pages\ListConstructionTypes::route('/'),
            'create' => Pages\CreateConstructionType::route('/create'),
            'edit' => Pages\EditConstructionType::route('/{record}/edit'),
        ];
    }
}
