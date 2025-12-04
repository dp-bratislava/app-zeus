<?php

namespace App\Filament\Resources\TS\TicketResource\Components;

use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Forms\Components\Component;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class MaterialRepeater
{
    public static function make(string $uri): Component
    {
        return TableRepeater::make($uri)
            ->defaultItems(0)
            ->cloneable()
            ->columns(12)
            ->headers([
                Header::make('date'),
                Header::make('code'),
                Header::make('title'),
                Header::make('description'),
                Header::make('price'),
                Header::make('VAT'),
                Header::make('attachments'),
            ])
            ->addable(false)            
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->columnSpan(1)
                    ->default(now()),
                Forms\Components\TextInput::make('code')
                    ->columnSpan(1),
                Forms\Components\TextInput::make('title')
                    ->columnSpan(2),
                
                Forms\Components\TextInput::make('description')
                    ->columnSpan(2),
                Forms\Components\TextInput::make('price')
                    ->columnSpan(1)
                    ->numeric()
                    ->inputMode('decimal'),
                Forms\Components\TextInput::make('vat')
                    ->columnSpan(1)
                    ->numeric()
                    ->inputMode('decimal'),
                SpatieMediaLibraryFileUpload::make('attachments')
                    ->columnSpan(2)
                    ->multiple()
            ]);
    }
}
