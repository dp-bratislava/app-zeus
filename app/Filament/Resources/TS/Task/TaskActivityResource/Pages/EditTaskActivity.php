<?php

namespace App\Filament\Resources\TS\Task\TaskActivityResource\Pages;

use App\Filament\Resources\TS\Task\TaskActivityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTaskActivity extends EditRecord
{
    protected static string $resource = TaskActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
