<?php

namespace App\Filament\Resources\Ticket;

use App\Filament\Resources\Ticket\TicketResource\Forms\TicketForm;
use App\Filament\Resources\Ticket\TicketResource\Pages;
use App\Filament\Resources\Ticket\TicketResource\Tables\TicketTable;
use Dpb\Package\Tickets\Models\Ticket;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    public static function getModelLabel(): string
    {
        return __('tickets/ticket.resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('tickets/ticket.resource.plural_model_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('tickets/ticket.navigation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('tickets/ticket.navigation.group');
    }

    public static function form(Form $form): Form
    {
        return TicketForm::make($form);
    }

    public static function table(Table $table): Table
    {
        return TicketTable::make($table);
    }

    public static function getRelations(): array
    {
        return [
            // ActivitiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            // 'view' => Pages\ViewTicket::route('/{record}'),
            'view' => Pages\ViewTicketPage::route('/{record}'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }

    // public static function canCreate(): bool
    // {
    //     return auth()->check() && auth()->user()->can('tickets.ticket.create');
    // }

    // public static function canEdit(Model $record): bool
    // {
    //     return auth()->check() && auth()->user()->can('tickets.ticket.update');
    // }   
    
    // public static function canDelete(Model $record): bool
    // {        
    //     return auth()->check() && auth()->user()->can('tickets.ticket.delete');
    // }       
}
