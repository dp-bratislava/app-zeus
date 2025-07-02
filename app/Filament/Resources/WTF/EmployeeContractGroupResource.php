<?php

namespace App\Filament\Resources\WTF;

use App\Filament\Resources\WTF\EmployeeContractGroupResource\Pages;
use App\Filament\Resources\WTF\EmployeeContractGroupResource\RelationManagers;
use App\Models\WTF\EmployeeContractGroup;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeContractGroupResource extends Resource
{
    protected static ?string $model = EmployeeContractGroup::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): ?string
    {
        return 'WTF';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title'),
                TextInput::make('description'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('description'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(DepartmentGroupImporter::class)
                    ->csvDelimiter(';')
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
            'index' => Pages\ListEmployeeContractGroups::route('/'),
            'create' => Pages\CreateEmployeeContractGroup::route('/create'),
            'edit' => Pages\EditEmployeeContractGroup::route('/{record}/edit'),
        ];
    }
}
