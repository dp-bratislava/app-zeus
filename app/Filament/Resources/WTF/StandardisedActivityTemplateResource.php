<?php

namespace App\Filament\Resources\WTF;

use App\Filament\Imports\WTF\StandardisedActivityTemplateImporter;
use App\Filament\Resources\WTF\StandardisedActivityTemplateResource\Pages;
use App\Filament\Resources\WTF\StandardisedActivityTemplateResource\RelationManagers;
use App\Models\WTF\StandardisedActivityTemplate;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StandardisedActivityTemplateResource extends Resource
{
    protected static ?string $model = StandardisedActivityTemplate::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Normované činnosti';
    protected static ?string $pluralModelLabel = 'Normované činnosti';
    protected static ?string $ModelLabel = 'Normovaná činnosť';
    public static function getNavigationGroup(): ?string
    {
        return 'WTF';
    }
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title'),
                TextInput::make('duration')->numeric(),
                Toggle::make('is_divisible'),
                TextInput::make('people')
                    ->numeric()
                    ->default(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)           
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('duration'),
                IconColumn::make('is_divisible')
                    ->boolean(),
                TextColumn::make('people'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(StandardisedActivityTemplateImporter::class)
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
            'index' => Pages\ListStandardisedActivityTemplates::route('/'),
            'create' => Pages\CreateStandardisedActivityTemplate::route('/create'),
            'edit' => Pages\EditStandardisedActivityTemplate::route('/{record}/edit'),
        ];
    }
}
