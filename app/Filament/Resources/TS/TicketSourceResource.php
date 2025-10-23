<?php

namespace App\Filament\Resources\TS;

use App\Filament\Resources\TS\TicketSourceResource\Pages;
use App\Filament\Resources\TS\TicketSourceResource\RelationManagers;
use Dpb\Package\Tickets\Models\TicketSource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketSourceResource extends Resource
{
    protected static ?string $model = TicketSource::class;

    public static function getModelLabel(): string
    {
        return __('tickets/ticket-source.resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('tickets/ticket-source.resource.plural_model_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('tickets/ticket-source.navigation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('tickets/ticket-source.navigation.group');
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
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label(__('tickets/ticket-source.table.columns.code.label')),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('tickets/ticket-source.table.columns.title.label')),
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
            'index' => Pages\ListTicketSources::route('/'),
            'create' => Pages\CreateTicketSource::route('/create'),
            'edit' => Pages\EditTicketSource::route('/{record}/edit'),
        ];
    }
}
