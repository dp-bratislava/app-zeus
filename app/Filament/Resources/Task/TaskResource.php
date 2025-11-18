<?php

namespace App\Filament\Resources\Task;

use App\Filament\Resources\Task\TaskResource\Pages;
use App\Filament\Resources\Task\TaskResource\RelationManagers;
use App\Models\Task\Task;
use Dpb\Package\TaskMS\Infrastructure\Persistence\Eloquent\Models\EloquentTask;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;

class TaskResource extends Resource
{
    protected static ?string $model = EloquentTask::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('date')->default(Carbon::now()),
                TextInput::make('title'),
                TextInput::make('description'),
                Select::make('group')
                    ->relationship('group', 'title')
                    ->searchable()
                    ->preload()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')->date(),
                TextColumn::make('title'),
                TextColumn::make('description'),
                TextColumn::make('group.title'),
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
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
