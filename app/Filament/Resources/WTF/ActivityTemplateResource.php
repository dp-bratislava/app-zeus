<?php

namespace App\Filament\Resources\WTF;

use App\Filament\Imports\WTF\ActivityTemplateImporter;
use App\Filament\Resources\WTF\ActivityTemplateResource\Pages;
use App\Filament\Resources\WTF\ActivityTemplateResource\RelationManagers;
use App\Models\WTF\ActivityTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ActivityTemplateResource extends Resource
{
    protected static ?string $model = ActivityTemplate::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                TextColumn::make('title'),
                TextColumn::make('description'),
                TextColumn::make('group.title'),
                TextColumn::make('type.title'),
                TextColumn::make('durations.duration')->badge(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(ActivityTemplateImporter::class)
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
            'index' => Pages\ListActivityTemplates::route('/'),
            'create' => Pages\CreateActivityTemplate::route('/create'),
            'edit' => Pages\EditActivityTemplate::route('/{record}/edit'),
        ];
    }
}
