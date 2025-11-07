<?php

namespace App\Filament\Resources\Fleet;

use App\Filament\Resources\Fleet\ServiceGroupResource\Pages;
use App\Filament\Resources\Fleet\ServiceGroupResource\RelationManagers;
use Dpb\Package\Fleet\Models\ServiceGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServiceGroupResource extends Resource
{
    protected static ?string $model = ServiceGroup::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Technické prevádzky';
    protected static ?string $pluralModelLabel = 'Technické prevádzky';
    protected static ?string $ModelLabel = 'Technická prevádzka';

    public static function canViewAny(): bool
    {
        return false;
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Fleet';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('short_title')->required(),
                Forms\Components\TextInput::make('title')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->columns([
                Tables\Columns\TextColumn::make('short_title'),
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
            'index' => Pages\ListServiceGroups::route('/'),
            'create' => Pages\CreateServiceGroup::route('/create'),
            'edit' => Pages\EditServiceGroup::route('/{record}/edit'),
        ];
    }
}
