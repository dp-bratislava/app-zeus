<?php

namespace App\Filament\Resources\DailyExpeditionResource\Tables;

use App\Models\DispatchReport;
use App\Services\Fleet\VehicleService;
use App\Services\Inspection\CreateTicketService;
use App\Services\Inspection\AssignmentService as InspectionAssignmentService;
use App\Services\TS\TicketAssignmentService;
use App\States;
use Dpb\Package\Inspections\Models\Inspection;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class DailyExpeditionTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100)
            ->recordClasses(fn($record) => match ($record->state) {
                'in-service' => 'bg-green-200',
                'split-service' => 'bg-yellow-200',
                'out-of-service' => 'bg-red-200',
                default => null,
            })
            ->columns([
                // date
                Tables\Columns\TextColumn::make('date')->date('j.n.Y')
                    ->label(__('daily-expedition.table.columns.date')),
                    // vehicle code
                Tables\Columns\TextColumn::make('vehicle.code.code')
                    ->label(__('daily-expedition.table.columns.vehicle')),
                    // vehicle model
                Tables\Columns\TextColumn::make('vehicle.model.title')
                    ->label(__('daily-expedition.table.columns.vehicle_model')),

                // Tables\Columns\TextColumn::make('state')
                //     ->label(__('daily-expedition.table.columns.state'))
                //     ->state(fn($record) => match ($record->state) {
                //         'ok' => 'Jazdí',
                //         'split' => 'Delená',
                //         'no' => 'Odstavený',
                //         default => null
                //     })
                //     ->badge()
                //     ->color(fn($record) => match ($record->state) {
                //         'ok' => 'success',
                //         'split' => 'warning',
                //         'no' => 'danger',
                //     }),
                // state
                Tables\Columns\SelectColumn::make('state')
                    ->label(__('tickets/ticket.table.columns.state'))
                    ->options([
                        'in-service' => __('daily-expedition.states.in-service'),
                        'split-service' => __('daily-expedition.states.split-service'),
                        'out-of-service' => __('daily-expedition.states.out-of-service'),
                    ]),
                // service                    
                Tables\Columns\TextColumn::make('service')
                    ->label(__('daily-expedition.table.columns.service'))
                    ->badge(),
                // note
                Tables\Columns\TextColumn::make('note')
                    ->label(__('daily-expedition.table.columns.note'))
                    ->grow(),
            ])
            ->filters(DailyExpeditionTableFilters::make());
    }
}
