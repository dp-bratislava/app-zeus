<?php

namespace App\Filament\Resources\Attendance;

use App\Filament\Imports\Attendance\CalendarGroupImporter;
use App\Filament\Resources\Attendance\CalendarGroupResource\Pages;
use App\Filament\Resources\Attendance\CalendarGroupResource\RelationManagers;
use App\Models\Attendance\CalendarGroup;
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

class CalendarGroupResource extends Resource
{
    protected static ?string $model = CalendarGroup::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    public static function getNavigationGroup(): ?string
    {
        return 'Attendance';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('code'),
                TextInput::make('title'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)        
            ->columns([
                TextColumn::make('code'),
                TextColumn::make('title'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(CalendarGroupImporter::class)
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
            'index' => Pages\ListCalendarGroups::route('/'),
            'create' => Pages\CreateCalendarGroup::route('/create'),
            'edit' => Pages\EditCalendarGroup::route('/{record}/edit'),
        ];
    }
}
