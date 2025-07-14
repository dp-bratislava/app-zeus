<?php

namespace App\Filament\Resources\WTF;

use App\Filament\Imports\WTF\StandardisedActivityGroupImporter;
use App\Filament\Resources\WTF\StandardisedActivityGroupResource\Pages;
use App\Filament\Resources\WTF\StandardisedActivityGroupResource\RelationManagers;
use App\Models\WTF\StandardisedActivityGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StandardisedActivityGroupResource extends Resource
{
    protected static ?string $model = StandardisedActivityGroup::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Skupiny normovaných činností';
    protected static ?string $pluralModelLabel = 'Skupiny normovaných činností';
    protected static ?string $ModelLabel = 'Normovaná činnosť';
    public static function getNavigationGroup(): ?string
    {
        return 'WTF';
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
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('description'),
                Tables\Columns\TextColumn::make('parent.title'),
                Tables\Columns\TextColumn::make('department.code'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(StandardisedActivityGroupImporter::class)
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
            'index' => Pages\ListStandardisedActivityGroups::route('/'),
            'create' => Pages\CreateStandardisedActivityGroup::route('/create'),
            'edit' => Pages\EditStandardisedActivityGroup::route('/{record}/edit'),
        ];
    }
}
