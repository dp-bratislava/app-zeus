<?php

namespace App\Filament\Resources\TS;

use App\Filament\Resources\TS\TicketGroupResource\Forms\TicketGroupForm;
use App\Filament\Resources\TS\TicketGroupResource\Pages;
use App\Filament\Resources\TS\TicketGroupResource\Tables\TicketGroupTable;
use Dpb\Package\Tickets\Models\TicketGroup;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

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

    public static function getNavigationSort(): ?int
    {
        return config('pkg-tickets.navigation.ticket-group') ?? 999;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('tickets.ticket-group.read');
    }

    public static function form(Form $form): Form
    {
        return TicketGroupForm::make($form);
    }

    public static function table(Table $table): Table
    {
        return TicketGroupTable::make($table);
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
