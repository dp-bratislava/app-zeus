<?php

namespace App\Filament\Resources\Activity;

use App\Filament\Imports\Activity\ActivityTemplateGroupImporter;
use App\Filament\Resources\Activity\TemplateGroupResource\Pages;
use App\Filament\Resources\Activity\TemplateGroupResource\RelationManagers;
use Dpb\Package\Activities\Models\TemplateGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TemplateGroupResource extends Resource
{
    protected static ?string $model = TemplateGroup::class;
    protected static ?string $navigationLabel = 'Skupiny noriem';
    protected static ?string $pluralModelLabel = 'Skupiny noriem';
    protected static ?string $ModelLabel = 'Skupin noriem';

    public static function getNavigationGroup(): ?string
    {
        return 'Normy';
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
                    ->importer(ActivityTemplateGroupImporter::class)
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
            'index' => Pages\ListTemplateGroups::route('/'),
            'create' => Pages\CreateTemplateGroup::route('/create'),
            'edit' => Pages\EditTemplateGroup::route('/{record}/edit'),
        ];
    }
}
