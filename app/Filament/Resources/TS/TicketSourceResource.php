<?php

namespace App\Filament\Resources\TS;

use App\Filament\Resources\TS\TicketSourceResource\Forms\TicketSourceForm;
use App\Filament\Resources\TS\TicketSourceResource\Pages;
use App\Filament\Resources\TS\TicketSourceResource\RelationManagers;
use App\Filament\Resources\TS\TicketSourceResource\Tables\TicketSourceTable;
use Dpb\Package\Tickets\Models\TicketSource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

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

    public static function getNavigationSort(): ?int
    {
        return config('pkg-tickets.navigation.ticket-source') ?? 999;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('tickets.ticket-source.read');
    }

    public static function form(Form $form): Form
    {
        return TicketSourceForm::make($form);
    }

    public static function table(Table $table): Table
    {
        return TicketSourceTable::make($table);
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
