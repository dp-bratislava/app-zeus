<?php

namespace App\Filament\Resources\TS\Task;

use App\Filament\Imports\TS\TaskTemplateGroupImporter;
use App\Filament\Resources\TS\Task\TemplateGroupResource\Pages;
use App\Filament\Resources\TS\Task\TemplateGroupResource\RelationManagers;
use App\Models\TS\Task\TemplateGroup;
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

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Skupiny úloh';
    protected static ?string $pluralModelLabel = 'Skupiny úloh';
    protected static ?string $ModelLabel = 'Skupina úloh';
    public static function canViewAny(): bool
    {
        return false;
    }
    public static function getNavigationGroup(): ?string
    {
        return 'TS';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('parent_id')
                    ->relationship('parent', 'title'),
                // ->options(),
                Forms\Components\TextInput::make('title'),
                Forms\Components\TextInput::make('description'),
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
                    ->importer(TaskTemplateGroupImporter::class)
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
