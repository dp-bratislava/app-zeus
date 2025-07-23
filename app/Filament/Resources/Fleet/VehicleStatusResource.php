<?php

namespace App\Filament\Resources\Fleet;

use App\Filament\Resources\Fleet\VehicleStatusResource\Pages;
use App\Filament\Resources\Fleet\VehicleStatusResource\RelationManagers;
use App\Models\Fleet\VehicleStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VehicleStatusResource extends Resource
{
    protected static ?string $model = VehicleStatus::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
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
            'index' => Pages\ListVehicleStatuses::route('/'),
            'create' => Pages\CreateVehicleStatus::route('/create'),
            'edit' => Pages\EditVehicleStatus::route('/{record}/edit'),
        ];
    }
}
