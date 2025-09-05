<?php

namespace App\Filament\Resources\TS;

use App\Filament\Resources\TS\StandardisedActivityResource\Pages;
use App\Filament\Resources\TS\StandardisedActivityResource\RelationManagers;
use App\Models\TS\StandardisedActivity;
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
    public static function canViewAny(): bool
    {
        return false;
    }

    public static function getNavigationGroup(): ?string
    {
        return 'temp';
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
            ->columns([
                //
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
