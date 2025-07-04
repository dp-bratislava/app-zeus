<?php

namespace App\Filament\Resources\WTF;

use App\Filament\Imports\WTF\ActivityGroupImporter;
use App\Filament\Resources\WTF\ActivityGroupResource\Pages;
use App\Filament\Resources\WTF\ActivityGroupResource\RelationManagers;
use App\Models\WTF\ActivityGroup;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ActivityGroupResource extends Resource
{
    protected static ?string $model = ActivityGroup::class;

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
                Select::make('parent')
                    ->relationship('parent', 'title'),
                Repeater::make('activities')
                    ->relationship('activities')
                    ->schema([
                        TextInput::make('title'),
                        TextInput::make('description'),
                        Toggle::make('is_locked'),
                        Repeater::make('durations')
                            ->relationship('durations')
                            ->schema([
                                TextInput::make('duration')->numeric(),
                            ]),
                        Select::make('type_id')
                            ->relationship('type', 'title'),

                    ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->columns([
                TextColumn::make('parent.title'),
                TextColumn::make('title'),
                TextColumn::make('description'),
                TextColumn::make('activities.title')
                    ->badge(),
                TextColumn::make('department.code'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(ActivityGroupImporter::class)
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
            'index' => Pages\ListActivityGroups::route('/'),
            'create' => Pages\CreateActivityGroup::route('/create'),
            'edit' => Pages\EditActivityGroup::route('/{record}/edit'),
        ];
    }
}
