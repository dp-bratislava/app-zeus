<?php

namespace App\Filament\Resources\TS\Task;

use App\Filament\Resources\TS\Task\TaskActivityResource\Pages;
use App\Filament\Resources\TS\Task\TaskActivityResource\RelationManagers;
use App\Models\TS\Task\TaskActivity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TaskActivityResource extends Resource
{
    protected static ?string $model = TaskActivity::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Činnosti';
    protected static ?string $pluralModelLabel = 'Činnosti';
    protected static ?string $ModelLabel = 'Činnosť';

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
                Tables\Columns\TextColumn::make('date')->date(),
                Tables\Columns\TextColumn::make('task.ticket.id')->label('ticketID'),
                Tables\Columns\TextColumn::make('task.id')->label('taskID'),
                Tables\Columns\TextColumn::make('task.template.title'),
                Tables\Columns\TextColumn::make('time_from')->time(),
                Tables\Columns\TextColumn::make('time_to')->time(),
                Tables\Columns\TextColumn::make('employeeContract.employee.fullName'),
                Tables\Columns\TextColumn::make('note'),
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
            'index' => Pages\ListTaskActivities::route('/'),
            'create' => Pages\CreateTaskActivity::route('/create'),
            'edit' => Pages\EditTaskActivity::route('/{record}/edit'),
        ];
    }
}
