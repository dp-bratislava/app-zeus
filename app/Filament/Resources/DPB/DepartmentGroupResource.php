<?php

namespace App\Filament\Resources\DPB;

use App\Filament\Resources\DPB\DepartmentGroupResource\Pages;
use App\Filament\Resources\DPB\DepartmentGroupResource\RelationManagers;
use App\Models\DPB\DepartmentGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DepartmentGroupResource extends Resource
{
    protected static ?string $model = DepartmentGroup::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Skupiny stredísk';
    protected static ?string $pluralModelLabel = 'Skupiny stredísk';
    protected static ?string $ModelLabel = 'Skupina stredísk';

    public static function getNavigationGroup(): ?string
    {
        return 'DPB';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code'),
                Forms\Components\TextInput::make('title'),
                Forms\Components\TextInput::make('description'),
                // Forms\Components\Select::make('departments')
                //     ->relationship('deparmtents', 'code')
                //     ->multiple()
                //     ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->columns([
                Tables\Columns\TextColumn::make('code'),
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('description'),
                Tables\Columns\TextColumn::make('departmetns.code')->badge(),
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
            'index' => Pages\ListDepartmentGroups::route('/'),
            'create' => Pages\CreateDepartmentGroup::route('/create'),
            'edit' => Pages\EditDepartmentGroup::route('/{record}/edit'),
        ];
    }
}
