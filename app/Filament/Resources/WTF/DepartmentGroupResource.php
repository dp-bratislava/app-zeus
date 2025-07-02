<?php

namespace App\Filament\Resources\WTF;

use App\Filament\Imports\WTF\DepartmentGroupImporter;
use App\Filament\Resources\WTF\DepartmentGroupResource\Pages;
use App\Filament\Resources\WTF\DepartmentGroupResource\RelationManagers;
use App\Models\WTF\DepartmentGroup;
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

class DepartmentGroupResource extends Resource
{
    protected static ?string $model = DepartmentGroup::class;

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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)        
            ->columns([
                TextColumn::make('title'),
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
            'index' => Pages\ListDepartmentGroups::route('/'),
            'create' => Pages\CreateDepartmentGroup::route('/create'),
            'edit' => Pages\EditDepartmentGroup::route('/{record}/edit'),
        ];
    }
}
