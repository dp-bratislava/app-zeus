<?php

namespace App\Filament\Resources\TS;

use App\Filament\Resources\TS\TicketItemResource\Forms\TicketItemAssignmentForm;
use App\Filament\Resources\TS\TicketItemResource\Pages;
use App\Filament\Resources\TS\TicketItemResource\RelationManagers;
use App\Filament\Resources\TS\TicketItemResource\Tables\TicketItemAssignmentTable;
use App\Filament\Resources\TS\TicketItemResource\Tables\TicketItemTable;
use App\Models\TicketItemAssignment;
use Dpb\Package\Tickets\Models\TicketItem;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class TicketItemResource extends Resource
{
    protected static ?string $model = TicketItemAssignment::class;

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

    public static function getNavigationSort(): ?int
    {
        return config('pkg-tickets.navigation.ticket-item') ?? 999;
    }

    public static function form(Form $form): Form
    {
        return TicketItemAssignmentForm::make($form);
    }

    public static function table(Table $table): Table
    {
        return TicketItemAssignmentTable::make($table);
        // return TicketItemTable::make($table);
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
