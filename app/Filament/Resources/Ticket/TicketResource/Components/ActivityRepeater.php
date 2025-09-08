<?php

namespace App\Filament\Resources\Ticket\TicketResource\Components;

use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Forms\Components\Component;
use Filament\Forms;


class ActivityRepeater
{
    public static function make(string $uri, string $relationship): Component
    {
        return TableRepeater::make($uri)
            ->defaultItems(0)
            ->cloneable()
            ->columnSpan(3)
            ->headers([
                Header::make('date'),
                // Header::make('template'),
                Header::make('status'),
            ])
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->default(now()),
                Forms\Components\Textarea::make('note'),
            ]);
    }
}
