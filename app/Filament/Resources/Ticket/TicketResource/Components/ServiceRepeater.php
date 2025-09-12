<?php

namespace App\Filament\Resources\Ticket\TicketResource\Components;

use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Forms\Components\Component;
use Filament\Forms;

class ServiceRepeater
{
    public static function make(string $uri): Component
    {
        return TableRepeater::make($uri)
            ->defaultItems(0)
            ->cloneable()
            ->columnSpan(5)
            ->headers([
                Header::make('date'),
                Header::make('code'),
                Header::make('title'),
                Header::make('description'),
                Header::make('price'),
                Header::make('VAT'),
                // Header::make('attachments'),
            ])
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->default(now()),
                Forms\Components\TextInput::make('code'),
                Forms\Components\TextInput::make('title'),
                Forms\Components\TextInput::make('description'),
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->inputMode('decimal'),
                Forms\Components\TextInput::make('vat')
                    ->numeric()
                    ->inputMode('decimal')
            ]);
    }
}
