<?php

namespace App\Filament\Resources\TS\TicketResource\Components;

use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Forms\Components\Component;
use Filament\Forms;
use App\Filament\Components\ContractPicker;


class WorkAssignmentRepeater
{
    public static function make(string $uri): Component
    {
        return TableRepeater::make($uri)
            ->defaultItems(0)
            ->cloneable()
            ->columnSpan(3)
            ->headers([
                Header::make('date'),
                Header::make('time_from'),
                Header::make('time_to'),
                Header::make('contract'),
                // Header::make('poznamka'),
                // Header::make('status'),
            ])
            ->schema([
                Forms\Components\Group::make()
                    // ->relationship('workInterval')
                    ->columns(3)
                    ->schema([
                        Forms\Components\DatePicker::make('date')
                            ->hiddenLabel()
                            ->columnSpan(1)
                            ->default(now()),

                        // Forms\Components\TextInput::make('duration')
                        //     ->numeric()
                        //     ->integer()
                        //     ->default(60),
                        Forms\Components\TimePicker::make('time_from')
                            ->hiddenLabel()
                            ->columnSpan(1),
                        Forms\Components\TimePicker::make('time_to')
                            ->hiddenLabel()
                            ->columnSpan(1),
                    ]),
                // contract
                ContractPicker::make('employee_contract_id')
                    ->relationship('employeeContract', 'pid')
                    ->getOptionLabelFromRecordUsing(null)
                    ->getSearchResultsUsing(null)
                    ->searchable(),
                // // note
                // Forms\Components\Textarea::make('note'),
            ])
            // ->mutateRelationshipDataBeforeCreateUsing(function (array $data, $get, $set, $livewire) {
            //     $ticketId = $livewire->record?->id;

            //     if ($ticketId) {
            //         $data['ticket_id'] = $ticketId;
            //     }

            //     return $data;
            // });
        ;
    }
}
