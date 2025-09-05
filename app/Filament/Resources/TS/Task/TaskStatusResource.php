<?php

namespace App\Filament\Resources\TS\Task;

use App\Filament\Resources\TS\Task\TaskStatusResource\Pages;
use App\Filament\Resources\TS\Task\TaskStatusResource\RelationManagers;
use App\Models\TS\Task\TaskStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TaskStatusResource extends Resource
{
    protected static ?string $model = TaskStatus::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Stavy úloh';
    protected static ?string $pluralModelLabel = 'Stavy úloh';
    protected static ?string $ModelLabel = 'Stav úlohy';
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
                Forms\Components\TextInput::make('code'),
                Forms\Components\TextInput::make('title'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->columns([
                Tables\Columns\TextColumn::make('code'),
                Tables\Columns\TextColumn::make('title'),
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
            'index' => Pages\ListTaskStatuses::route('/'),
            'create' => Pages\CreateTaskStatus::route('/create'),
            'edit' => Pages\EditTaskStatus::route('/{record}/edit'),
        ];
    }
}
