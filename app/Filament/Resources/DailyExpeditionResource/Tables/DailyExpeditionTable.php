<?php

namespace App\Filament\Resources\DailyExpeditionResource\Tables;

use App\Filament\Resources\DailyExpeditionResource;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;

class DailyExpeditionTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->heading(__('daily-expedition.table.heading'))
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
            ->filters(DailyExpeditionTableFilters::make())
            ->headerActions([
                Tables\Actions\Action::make('bulkCreate')
                    ->label('Vytvoriť')
                    ->color('primary')
                    ->icon('heroicon-o-plus')
                    ->url(DailyExpeditionResource::getUrl('bulk-create')),

                Tables\Actions\Action::make('bulkCreate2')
                    ->label('Vytvoriť 2')
                    ->color('primary')
                    ->icon('heroicon-o-plus')
                    ->url(DailyExpeditionResource::getUrl('bulk-create-2')),

            ]);
    }
}
