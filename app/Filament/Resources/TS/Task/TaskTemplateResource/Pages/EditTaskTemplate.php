<?php

namespace App\Filament\Resources\TS\Task\TaskTemplateResource\Pages;

use App\Filament\Resources\TS\Task\TaskTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTaskTemplate extends EditRecord
{
    protected static string $resource = TaskTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
