<?php

namespace App\Filament\Resources\TS;

use App\Filament\Resources\TS\TicketItemResource\Forms\TicketItemForm;
use App\Filament\Resources\TS\TicketItemResource\Pages;
use App\Filament\Resources\TS\TicketItemResource\RelationManagers;
use App\Filament\Resources\TS\TicketItemResource\Tables\TicketItemTable;
use Dpb\Package\Tickets\Models\TicketItem;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketItemResource extends Resource
{
    protected static ?string $model = TicketItem::class;

    public static function getModelLabel(): string
    {
        return __('tickets/ticket-item.resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('tickets/ticket-item.resource.plural_model_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('tickets/ticket-item.navigation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('tickets/ticket-item.navigation.group');
    }

    public static function form(Form $form): Form
    {
        return TicketItemForm::make($form);
    }

    public static function table(Table $table): Table
    {
        return TicketItemTable::make($table);
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
            'index' => Pages\ListTicketItems::route('/'),
            'create' => Pages\CreateTicketItem::route('/create'),
            'view' => Pages\ViewTicketItemPage::route('/{record}'),
            'edit' => Pages\EditTicketItem::route('/{record}/edit'),
        ];
    }
}
