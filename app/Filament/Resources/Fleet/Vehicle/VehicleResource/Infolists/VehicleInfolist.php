<?php

namespace App\Filament\Resources\Fleet\Vehicle\VehicleResource\Infolists;

use Filament\Infolists;
use Filament\Infolists\Infolist;

class VehicleInfolist
{
    public static function make(Infolist $infolist): Infolist
    {
        // dd($infolist->record->travelLog);
        $paramGroups = [
            'Vlastnosti',
            'tankovanie',
            'naplne',
            'km',
            'pneumatiky',
            'kontroly',
            'poruchy',
            'mimoriadn udalosti',
            'poistne udalosti',
            'dokumenty',
        ];

        $tabs = [];

        foreach ($paramGroups as $paramGroup) {
            $records = [];
            if ($paramGroup == 'km') {
                $travelRecords = $infolist->record->travelLog;
                foreach ($travelRecords as $travelRecord) {
                    $records[] = Infolists\Components\TextEntry::make($travelRecord->date)
                        ->inlineLabel()
                        ->state($travelRecord->distance);
                }
            } else {
                $records = [
                    Infolists\Components\TextEntry::make('param 1'),
                    Infolists\Components\TextEntry::make('param 2'),
                    Infolists\Components\TextEntry::make('param ...'),
                    Infolists\Components\TextEntry::make('param N'),
                ];
            }
            $tabs[] = Infolists\Components\Tabs\Tab::make($paramGroup)
                ->schema($records);
        }

        return $infolist
            ->schema([
                Infolists\Components\Tabs::make('tabs')
                    ->columns(1)
                    ->columnSpanFull()
                    ->tabs($tabs)
            ]);
    }
}
