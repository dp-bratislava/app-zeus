<?php

namespace App\Filament\Resources\TS;

use App\Filament\Imports\TS\TicketGroupImporter;
use App\Filament\Resources\TS\TicketGroupResource\Pages;
use App\Filament\Resources\TS\TicketGroupResource\RelationManagers;
use Dpb\Package\Tickets\Models\TicketGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Columns;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketGroupResource extends Resource
{
    protected static ?string $model = TicketGroup::class;

    public static function getModelLabel(): string
    {
        return __('tickets/ticket-group.resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('tickets/ticket-group.resource.plural_model_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('tickets/ticket-group.navigation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('tickets/ticket-group.navigation.group');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code'),
                Forms\Components\TextInput::make('title'),
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
                Tables\Columns\TextColumn::make('parent.title'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // ImportAction::make()
                //     ->importer(TicketGroupImporter::class)
                //     ->csvDelimiter(';')
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
            'index' => Pages\ListTicketGroups::route('/'),
            'create' => Pages\CreateTicketGroup::route('/create'),
            'edit' => Pages\EditTicketGroup::route('/{record}/edit'),
        ];
    }
}
