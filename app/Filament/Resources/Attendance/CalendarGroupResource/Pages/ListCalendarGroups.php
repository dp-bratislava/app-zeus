<?php

namespace App\Filament\Resources\Attendance\CalendarGroupResource\Pages;

use App\Filament\Resources\Attendance\CalendarGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCalendarGroups extends ListRecords
{
    protected static string $resource = CalendarGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
