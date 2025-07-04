<?php

namespace App\Filament\Resources\WTF\AttendanceActivityResource\Pages;

use App\Filament\Resources\WTF\AttendanceActivityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAttendanceActivities extends ListRecords
{
    protected static string $resource = AttendanceActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
