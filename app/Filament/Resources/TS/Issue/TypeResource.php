<?php

namespace App\Filament\Resources\TS\Issue;

use App\Filament\Imports\TS\IssueTypeImporter;
use App\Filament\Resources\TS\Issue\TypeResource\Pages;
use App\Filament\Resources\TS\Issue\TypeResource\RelationManagers;
use App\Models\TS\Issue\Type;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TypeResource extends Resource
{
    protected static ?string $model = Type::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Typy porúch';
    protected static ?string $pluralModelLabel = 'Typy porúch';
    protected static ?string $ModelLabel = 'Typ poruchy';
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
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)           
            ->columns([
                Tables\Columns\TextColumn::make('departmentGroup.code'),
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('description'),
                Tables\Columns\TextColumn::make('parent.title'),
            ])
            ->filters([
                //
            ])
            ->headerActions([

                ImportAction::make()
                ->importer(IssueTypeImporter::class)
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
            'index' => Pages\ListTypes::route('/'),
            'create' => Pages\CreateType::route('/create'),
            'edit' => Pages\EditType::route('/{record}/edit'),
        ];
    }
}
