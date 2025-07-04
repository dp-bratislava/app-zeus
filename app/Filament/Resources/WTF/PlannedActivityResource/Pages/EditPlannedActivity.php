<?php

namespace App\Filament\Resources\WTF\PlannedActivityResource\Pages;

use App\Filament\Resources\WTF\PlannedActivityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlannedActivity extends EditRecord
{
    protected static string $resource = PlannedActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
