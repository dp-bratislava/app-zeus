<?php

namespace App\Filament\Resources\Fleet\Inspection;

use App\Filament\Imports\Fleet\InspectionTemplateImporter;
use App\Filament\Resources\Fleet\Inspection\InspectionTemplateResource\Pages;
use Dpb\Packages\Vehicles\Models\Inspection\InspectionTemplate;
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
    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Šablóny kontrol';
    protected static ?string $pluralModelLabel = 'Šablóny kontrol';
    protected static ?string $ModelLabel = 'Šablóna kontroly';

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
                Forms\Components\TextInput::make('title')->required(),
                Forms\Components\TextInput::make('distance_interval')->integer(),
                Forms\Components\TextInput::make('distance_first_advance')->integer(),
                Forms\Components\TextInput::make('distance_second_advance')->integer(),
                Forms\Components\TextInput::make('time_interval')->integer(),
                Forms\Components\TextInput::make('time_first_advance')->integer(),
                Forms\Components\TextInput::make('time_second_advance')->integer(),
                Forms\Components\Toggle::make('is_one_time'),
                Forms\Components\Toggle::make('is_periodical'),
                Forms\Components\TextInput::make('note'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('distance_interval')->label('KM'),
                Tables\Columns\TextColumn::make('distance_first_advance')->label('KM 1'),
                Tables\Columns\TextColumn::make('distance_second_advance')->label('KM 2'),
                Tables\Columns\TextColumn::make('time_interval')->label('time'),
                Tables\Columns\TextColumn::make('time_first_advance')->label('time 1'),
                Tables\Columns\TextColumn::make('time_second_advance')->label('time 2'),
                Tables\Columns\IconColumn::make('is_one_time')->boolean(),
                Tables\Columns\IconColumn::make('is_periodical')->boolean(),
                Tables\Columns\TextColumn::make('note'),
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
