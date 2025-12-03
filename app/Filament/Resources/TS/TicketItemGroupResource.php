<?php

namespace App\Filament\Resources\TS;

use App\Filament\Resources\TS\TicketItemGroupResource\Forms\TicketItemGroupForm;
use App\Filament\Resources\TS\TicketItemGroupResource\Pages;
use App\Filament\Resources\TS\TicketItemGroupResource\Tables\TicketItemGroupTable;
use Dpb\Package\Tickets\Models\TicketItemGroup;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class TicketItemGroupResource extends Resource
{
    protected static ?string $model = TicketItemGroup::class;

    public static function getModelLabel(): string
    {
        return __('tickets/ticket-item-group.resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('tickets/ticket-item-group.resource.plural_model_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('tickets/ticket-item-group.navigation.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('tickets/ticket-item-group.navigation.group');
    }

    public static function getNavigationSort(): ?int
    {
        return config('pkg-tickets.navigation.ticket-item-group') ?? 999;
    }

    // public static function canViewAny(): bool
    // {
    //     // return auth()->user()->can('tickets.ticket-item.read');
    //     return false;
    // }

    public static function form(Form $form): Form
    {
        return TicketItemGroupForm::make($form);
    }

    public static function table(Table $table): Table
    {
        return TicketItemGroupTable::make($table);
        // return TicketItemGroupTable::make($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTicketItemGroups::route('/'),
            'create' => Pages\CreateTicketItemGroup::route('/create'),
            // 'view' => Pages\ViewTicketItemGroupPage::route('/{record}'),
            'edit' => Pages\EditTicketItemGroup::route('/{record}/edit'),
        ];
    }
}
