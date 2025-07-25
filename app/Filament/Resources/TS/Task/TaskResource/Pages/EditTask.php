<?php

namespace App\Filament\Resources\TS\Task\TaskResource\Pages;

use App\Filament\Resources\TS\Task\TaskResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTask extends EditRecord
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
