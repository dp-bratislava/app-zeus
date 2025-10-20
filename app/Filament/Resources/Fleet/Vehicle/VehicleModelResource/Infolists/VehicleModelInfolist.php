<?php

namespace App\Filament\Resources\Fleet\Vehicle\VehicleModelResource\Infolists;

use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;

class VehicleModelInfolist extends Resource
{
    public static function make(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Tabs::make('tabs')
                    ->columns(1)
                    ->columnSpanFull()
                    ->tabs([
                        Infolists\Components\Tabs\Tab::make('technicke parametre')
                            ->schema([
                                Infolists\Components\TextEntry::make('param 1'),
                                Infolists\Components\TextEntry::make('param 2'),
                                Infolists\Components\TextEntry::make('param ...'),
                                Infolists\Components\TextEntry::make('param N'),
                            ]),
                        Infolists\Components\Tabs\Tab::make('poistne udalosti')
                            ->schema([
                                Infolists\Components\TextEntry::make('param 1'),
                                Infolists\Components\TextEntry::make('param 2'),
                                Infolists\Components\TextEntry::make('param ...'),
                                Infolists\Components\TextEntry::make('param N'),
                            ]),
                        Infolists\Components\Tabs\Tab::make('poruchy')
                            ->schema([
                                Infolists\Components\TextEntry::make('param 1'),
                                Infolists\Components\TextEntry::make('param 2'),
                                Infolists\Components\TextEntry::make('param ...'),
                                Infolists\Components\TextEntry::make('param N'),
                            ]),
                        Infolists\Components\Tabs\Tab::make('material')
                            ->schema([
                                Infolists\Components\TextEntry::make('tickets.materials.title'),
                            ]),
                        Infolists\Components\Tabs\Tab::make('STK')
                            ->schema([
                                Infolists\Components\TextEntry::make('param 1'),
                                Infolists\Components\TextEntry::make('param 2'),
                                Infolists\Components\TextEntry::make('param ...'),
                                Infolists\Components\TextEntry::make('param N'),
                            ]),

                    ])
            ]);
    }

}
