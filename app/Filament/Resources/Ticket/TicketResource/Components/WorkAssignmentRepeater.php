<?php

namespace App\Filament\Resources\Ticket\TicketResource\Components;

use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Forms\Components\Component;
use Filament\Forms;
use App\Filament\Components\ContractPicker;


class WorkAssignmentRepeater
{
    public static function make(): Component
    {
        return TableRepeater::make('workAssignments')
            ->relationship('workAssignments')
            ->defaultItems(0)
            ->cloneable()
            ->columnSpan(3)
            ->headers([
                Header::make('date'),
                Header::make('time_from'),
                Header::make('time_to'),
                Header::make('description'),
                Header::make('contract'),
                Header::make('status'),
            ])
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->default(now()),
                // Forms\Components\TextInput::make('duration')
                //     ->numeric()
                //     ->integer()
                //     ->default(60),
                Forms\Components\TimePicker::make('time_from'),
                Forms\Components\TimePicker::make('time_to'),
                Forms\Components\Textarea::make('note'),
                ContractPicker::make('employee_contract_id')
                    ->relationship('employeeContract', 'pid')
                    ->getOptionLabelFromRecordUsing(null)
                    ->getSearchResultsUsing(null)
                    ->searchable(),
            ])
            ->mutateRelationshipDataBeforeCreateUsing(function (array $data, $get, $set, $livewire) {
                $ticketId = $livewire->record?->id;

                if ($ticketId) {
                    $data['ticket_id'] = $ticketId;
                }

                return $data;
            });
    }
}
