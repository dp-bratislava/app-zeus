<?php

namespace App\Filament\Resources\WTF;

use App\Filament\Resources\WTF\PlannedActivityResource\Pages;
use App\Filament\Resources\WTF\PlannedActivityResource\RelationManagers;
use App\Models\WTF\ActivityStatus;
use App\Models\WTF\PlannedActivity;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlannedActivityResource extends Resource
{
    protected static ?string $model = PlannedActivity::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): ?string
    {
        return 'WTF';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->default(now()),
                Forms\Components\TextInput::make('duration')
                    ->numeric()
                    ->integer()
                    ->default(60),
                Forms\Components\Select::make('template_id')
                    ->relationship('template', 'title')
                    ->preload()
                    ->searchable(),
                Forms\Components\Select::make('employee_contract_id')
                    ->relationship('employeeContract', 'pid')
                    ->searchable(),
                Forms\Components\ToggleButtons::make('status_id')
                    ->options(fn() => ActivityStatus::pluck('title', 'id'))
                    ->default(ActivityStatus::where('code', '=', 'undone')->first()->id),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->columns([
                TextColumn::make('date')->date(),
                TextColumn::make('template.title'),
                TextColumn::make('employeeContract.pid'),
                TextColumn::make('status.title'),
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
            'index' => Pages\ListPlannedActivities::route('/'),
            'create' => Pages\CreatePlannedActivity::route('/create'),
            'edit' => Pages\EditPlannedActivity::route('/{record}/edit'),
        ];
    }
}
