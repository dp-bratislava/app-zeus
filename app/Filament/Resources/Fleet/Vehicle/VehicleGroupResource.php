<?php

namespace App\Filament\Resources\Fleet\Vehicle;

use App\Filament\Imports\Fleet\VehicleGroupImporter;
use App\Filament\Resources\Fleet\Vehicle\VehicleGroupResource\Pages;
use App\Filament\Resources\Fleet\Vehicle\VehicleGroupResource\RelationManagers;
use Dpb\Package\Fleet\Models\VehicleGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VehicleGroupResource extends Resource
{
    protected static ?string $model = VehicleGroup::class;

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
                TextColumn::make('description'),
                // TextColumn::make('title'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(VehicleGroupImporter::class)
                    ->csvDelimiter(';')
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
            'index' => Pages\ListVehicleGroups::route('/'),
            'create' => Pages\CreateVehicleGroup::route('/create'),
            'edit' => Pages\EditVehicleGroup::route('/{record}/edit'),
        ];
    }
}
