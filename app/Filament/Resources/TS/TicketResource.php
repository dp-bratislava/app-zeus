<?php

namespace App\Filament\Resources\TS;

use App\Filament\Resources\TS\TicketResource\Forms\TicketAssignmentForm;
use App\Filament\Resources\TS\TicketResource\Forms\TicketForm;
use App\Filament\Resources\TS\TicketResource\Pages;
use App\Filament\Resources\TS\TicketResource\RelationManagers\TicketItemRelationManager;
use App\Filament\Resources\TS\TicketResource\Tables\TicketAssignmentTable;
use App\Filament\Resources\TS\TicketResource\Tables\TicketTable;
use App\Models\TicketAssignment;
use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Package\Tickets\Models\Ticket;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TicketResource extends Resource
{
    // protected static ?string $model = Ticket::class;
    protected static ?string $model = TicketAssignment::class;

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

    public static function getNavigationSort(): ?int
    {
        return config('pkg-tickets.navigation.ticket') ?? 999;
    }

    public static function form(Form $form): Form
    {
        // return TicketForm::make($form);
        return TicketAssignmentForm::make($form);
    }

    public static function table(Table $table): Table
    {
        // return TicketTable::make($table);
        return TicketAssignmentTable::make($table);
    }

    public static function getRelations(): array
    {
        return [
            // ActivitiesRelationManager::class,
            TicketItemRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'view' => Pages\ViewTicket::route('/{record}'),
            // 'view' => Pages\ViewTicketPage::route('/{record}'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }

    // public static function getEloquentQuery(): Builder
    // {
        // return parent::getEloquentQuery()
        //     ->where('subject_type', '=', app(Vehicle::class)->getMorphClass())
        //     ->whereHas('subject', function ($q) {
        //         $userHandledVehicleTypes = auth()->user()->vehicleTypes();
        //         $q->byType($userHandledVehicleTypes);
        //     });
    // }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('tickets.ticket.read');
    }

    public static function canCreate(): bool
    {
        return auth()->check() && auth()->user()->can('tickets.ticket.create');
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->check() && auth()->user()->can('tickets.ticket.update');
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->check() && auth()->user()->can('tickets.ticket.delete');
    }
}
