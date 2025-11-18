<?php

namespace App\Filament\Resources\Task;

use App\Filament\Resources\Task\TaskGroupResource\Pages;
use App\Filament\Resources\Task\TaskGroupResource\RelationManagers;
use Dpb\Package\TaskMS\Infrastructure\Persistence\Eloquent\Models\EloquentTaskGroup;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TaskGroupResource extends Resource
{
    protected static ?string $model = EloquentTaskGroup::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('code'),
                TextInput::make('title'),
                TextInput::make('description'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code'),
                TextColumn::make('title'),
                TextColumn::make('description'),
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
            'index' => Pages\ListTaskGroups::route('/'),
            'create' => Pages\CreateTaskGroup::route('/create'),
            'edit' => Pages\EditTaskGroup::route('/{record}/edit'),
        ];
    }
}
