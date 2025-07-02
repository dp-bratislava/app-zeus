<?php

namespace App\Filament\Resources\Attendance;

use App\Filament\Imports\Attendance\ShiftTemplateImporter;
use App\Filament\Resources\Attendance\ShiftTemplateResource\Pages;
use App\Filament\Resources\Attendance\ShiftTemplateResource\RelationManagers;
use App\Models\Attendance\ShiftTemplate;
use DateTime;
use Filament\Tables\Actions\ImportAction;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShiftTemplateResource extends Resource
{
    protected static ?string $model = ShiftTemplate::class;

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
                TimePicker::make('time_from'),
                TimePicker::make('time_to'),
                TextInput::make('duration')->numeric(),
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
                TextColumn::make('time_from'),
                TextColumn::make('time_to'),
                TextColumn::make('duration'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(ShiftTemplateImporter::class)
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
            'index' => Pages\ListShiftTemplates::route('/'),
            'create' => Pages\CreateShiftTemplate::route('/create'),
            'edit' => Pages\EditShiftTemplate::route('/{record}/edit'),
        ];
    }
}
