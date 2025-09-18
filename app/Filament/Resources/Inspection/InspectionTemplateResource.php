<?php

namespace App\Filament\Resources\Inspection;

use App\Filament\Imports\Inspection\InspectionTemplateImporter;
use App\Filament\Resources\Inspection\InspectionTemplateResource\Pages;
use App\Filament\Resources\Inspection\InspectionTemplateResource\RelationManagers;
use App\Models\Fleet\VehicleModel;
use Dpb\Package\Inspections\Models\InspectionTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InspectionTemplateResource extends Resource
{
    protected static ?string $model = InspectionTemplate::class;

    protected static ?string $navigationLabel = 'Šablóny kontrol';
    protected static ?string $pluralModelLabel = 'Šablóny kontrol';
    protected static ?string $ModelLabel = 'Šablóna kontroly';

    // public static function canViewAny(): bool
    // {
    //     return false;
    // }

    public static function getNavigationGroup(): ?string
    {
        return 'Kontroly';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code'),
                Forms\Components\TextInput::make('title'),
                Forms\Components\Select::make('vehicleModels')
                    ->relationship('vehicleModels', 'title')
                    ->multiple()
                    ->preload()
                    ->searchable()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('vehicleModels.title')
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(InspectionTemplateImporter::class)
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
            'index' => Pages\ListInspectionTemplates::route('/'),
            'create' => Pages\CreateInspectionTemplate::route('/create'),
            'edit' => Pages\EditInspectionTemplate::route('/{record}/edit'),
        ];
    }
}
