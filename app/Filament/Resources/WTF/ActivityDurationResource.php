<?php

namespace App\Filament\Resources\WTF;

use App\Filament\Resources\WTF\ActivityDurationResource\Pages;
use App\Filament\Resources\WTF\ActivityDurationResource\RelationManagers;
use App\Models\WTF\ActivityDuration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ActivityDurationResource extends Resource
{
    protected static ?string $model = ActivityDuration::class;

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
            'index' => Pages\ListActivityDurations::route('/'),
            'create' => Pages\CreateActivityDuration::route('/create'),
            'edit' => Pages\EditActivityDuration::route('/{record}/edit'),
        ];
    }
}
