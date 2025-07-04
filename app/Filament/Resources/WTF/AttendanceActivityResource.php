<?php

namespace App\Filament\Resources\WTF;

use App\Filament\Resources\WTF\AttendanceActivityResource\Pages;
use App\Filament\Resources\WTF\AttendanceActivityResource\RelationManagers;
use App\Models\WTF\AttendanceActivity;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttendanceActivityResource extends Resource
{
    protected static ?string $model = AttendanceActivity::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): ?string
    {
        return 'WTF';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TimePicker::make('time_from'),
                TimePicker::make('time_to'),
                Select::make('task_id')
                    ->relationship('task', 'title'),
                Select::make('activity_id')
                    ->relationship('activity', 'title'),
                // Select::make('standardised_activity_id')
                //     ->relationship('standarisedActivity', 'template.title'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->columns([
                TextColumn::make('task.title'),
                TextColumn::make('attendance.contract.pid'),
                TextColumn::make('standardisedActivity.template.title'),
                TextColumn::make('activity.title'),
                TextColumn::make('time_from'),
                TextColumn::make('time_to'),
                TextColumn::make('duration'),
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
            'index' => Pages\ListAttendanceActivities::route('/'),
            'create' => Pages\CreateAttendanceActivity::route('/create'),
            'edit' => Pages\EditAttendanceActivity::route('/{record}/edit'),
        ];
    }
}
