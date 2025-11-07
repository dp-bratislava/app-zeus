<?php

namespace App\Filament\Resources\Fleet\Vehicle;

use App\Filament\Imports\Fleet\VehicleStatusImporter;
use App\Filament\Resources\Fleet\Vehicle\VehicleStatusResource\Pages;
use Dpb\Package\Fleet\Models\VehicleStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VehicleStatusResource extends Resource
{
    protected static ?string $model = VehicleStatus::class;

    public static function canViewAny(): bool
    {
        return false;
    }

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
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
                TextColumn::make('code'),
                TextColumn::make('title'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(VehicleStatusImporter::class)
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
            'index' => Pages\ListVehicleStatuses::route('/'),
            'create' => Pages\CreateVehicleStatus::route('/create'),
            'edit' => Pages\EditVehicleStatus::route('/{record}/edit'),
        ];
    }
}
