<?php

namespace App\Filament\Resources\WTF;

use App\Filament\Resources\WTF\StandardisedActivityResource\Pages;
use App\Filament\Resources\WTF\StandardisedActivityResource\RelationManagers;
use App\Models\WTF\StandardisedActivity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StandardisedActivityResource extends Resource
{
    protected static ?string $model = StandardisedActivity::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): ?string
    {
        return 'WTF';
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
                Tables\Columns\TextColumn::make('task.title'),
                Tables\Columns\TextColumn::make('template.title'),
                Tables\Columns\TextColumn::make('template.duration')
                    ->label('trvanie podla normy'),
                Tables\Columns\TextColumn::make('activities.duration_sum')
                    ->label('skutocne trvanie')
                    ->state(function ($record) {
                        $result = $record->activities->sum('duration');
                        return $result;
                    }),
                Tables\Columns\TextColumn::make('template.group.department.code'),
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
            'index' => Pages\ListStandardisedActivities::route('/'),
            'create' => Pages\CreateStandardisedActivity::route('/create'),
            'edit' => Pages\EditStandardisedActivity::route('/{record}/edit'),
        ];
    }
}
