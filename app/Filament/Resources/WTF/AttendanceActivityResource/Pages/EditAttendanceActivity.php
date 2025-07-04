<?php

namespace App\Filament\Resources\WTF\AttendanceActivityResource\Pages;

use App\Filament\Resources\WTF\AttendanceActivityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAttendanceActivity extends EditRecord
{
    protected static string $resource = AttendanceActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
