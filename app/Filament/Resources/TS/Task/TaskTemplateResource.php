<?php

namespace App\Filament\Resources\TS\Task;

use App\Filament\Resources\TS\Task\TaskTemplateResource\Pages;
use App\Filament\Resources\TS\Task\TaskTemplateResource\RelationManagers;
use App\Models\TS\Task\TaskTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TaskTemplateResource extends Resource
{
    protected static ?string $model = TaskTemplate::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Šablóny úloh';
    protected static ?string $pluralModelLabel = 'Šablóny úloh';
    protected static ?string $ModelLabel = 'Šablóna úloh';
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
                Forms\Components\Select::make('template_group_id')
                    ->relationship('templateGroup', 'title'),
                // ->options(),
                Forms\Components\TextInput::make('title'),
                Forms\Components\TextInput::make('duration')->numeric(),
                Forms\Components\Toggle::make('is_divisible'),
                Forms\Components\TextInput::make('people')
                    ->numeric()
                    ->default(1),
                Forms\Components\Toggle::make('is_catalogised'),
                Forms\Components\Toggle::make('is_standardised'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('duration'),
                Tables\Columns\IconColumn::make('is_divisible')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_standardised')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_catalogised')
                    ->boolean(),
                Tables\Columns\TextColumn::make('people'),
            ])
            ->filters([
                //
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
            'index' => Pages\ListTaskTemplates::route('/'),
            'create' => Pages\CreateTaskTemplate::route('/create'),
            'edit' => Pages\EditTaskTemplate::route('/{record}/edit'),
        ];
    }
}
