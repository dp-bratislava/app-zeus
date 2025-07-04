<?php

namespace App\Filament\Resources\WTF;

use App\Filament\Resources\WTF\TaskResource\Pages;
use App\Filament\Resources\WTF\TaskResource\RelationManagers;
use App\Models\WTF\Task;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function getNavigationGroup(): ?string
    {
        return 'WTF';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('date')->default(now()),
                TextInput::make('title'),
                TextInput::make('description'),
                // Select::make('parent_id')
                //     ->relationship('parent', 'title')
                //     ->searchable()
                //     ->required(false),
                Select::make('department_id')
                    ->relationship('department', 'code')
                    ->searchable(),

                    // standardised activities 
                Repeater::make('standardisedActivities')
                    ->relationship('standardisedActivities')
                    ->schema([
                        Select::make('template_id')
                            ->relationship('template', 'title'),
                    ]),

                // Repeater::make('activities')
                //     ->relationship('activities')
                //     ->schema([
                //         TextInput::make('title'),
                //         TextInput::make('description'),
                //         Toggle::make('is_locked'),
                //         Repeater::make('durations')
                //             ->relationship('durations')
                //             ->schema([
                //                 TextInput::make('duration')->numeric(),
                //             ]),
                //         Select::make('type_id')
                //             ->relationship('type', 'title'),

                //     ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->columns([
                TextColumn::make('date')->date(),
                TextColumn::make('parent.id'),
                TextColumn::make('title'),
                TextColumn::make('description'),
                TextColumn::make('department.code'),
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
