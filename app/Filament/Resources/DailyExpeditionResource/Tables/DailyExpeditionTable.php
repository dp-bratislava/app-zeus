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
            // ->recordClasses(fn($record) => match ($record->state?->getValue()) {
            //     States\Inspection\Upcoming::$name => 'bg-blue-200',
            //     States\Inspection\InProgress::$name => 'bg-yellow-200',
            //     default => null,
            // })
            ->columns([
                Tables\Columns\TextColumn::make('date')->date('j.n.Y')
                    ->label(__('daily-expedition.table.columns.date')),
                Tables\Columns\TextColumn::make('vehicle.code.code')
                    ->label(__('daily-expedition.table.columns.vehicle')),
                Tables\Columns\TextColumn::make('state')
                    ->label(__('daily-expedition.table.columns.state'))
                    ->state(fn($record) => match ($record->state) {
                        'ok' => 'Jazdí',
                        'split' => 'Delená',
                        'no' => 'Odstavený',
                        default => null
                    })
                    ->badge()
                    ->color(fn($record) => match ($record->state) {
                        'ok' => 'success',
                        'split' => 'warning',
                        'no' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('service')
                    ->label(__('daily-expedition.table.columns.service')),
                Tables\Columns\TextColumn::make('note')
                    ->label(__('daily-expedition.table.columns.note')),
            ]);

    }
}
