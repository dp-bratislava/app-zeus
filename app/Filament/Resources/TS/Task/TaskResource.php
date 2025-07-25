<?php

namespace App\Filament\Resources\TS\Task;

use App\Filament\Resources\TS\Task\TaskResource\Pages;
use App\Filament\Resources\TS\Task\TaskResource\RelationManagers;
use App\Models\TS\Task\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Úlohy';
    protected static ?string $pluralModelLabel = 'Úlohy';
    protected static ?string $ModelLabel = 'Úloha';

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
                Tables\Columns\TextColumn::make('ticket.id'),
                Tables\Columns\TextColumn::make('date')->date(),
                Tables\Columns\TextColumn::make('status.title'),
                Tables\Columns\TextColumn::make('template.title')->label('uloha'),
                Tables\Columns\TextColumn::make('template.duration')->label('ocakavane trvanie'),
                Tables\Columns\TextColumn::make('real_duration')
                    ->label('realne trvanie')
                    ->state(function ($record) {
                        $result = $record->activities->sum('duration');
                        return $result;
                    }),                    
                Tables\Columns\IconColumn::make('template.is_divisible')
                    ->label('delitelna')
                    ->boolean(),
                Tables\Columns\IconColumn::make('template.is_standardised')
                    ->label('normovana')
                    ->boolean(),
                Tables\Columns\IconColumn::make('template.is_catalogised')
                    ->label('katalogizovana')
                    ->boolean(),
                Tables\Columns\TextColumn::make('template.people')
                    ->label('pocet ludi'),
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
