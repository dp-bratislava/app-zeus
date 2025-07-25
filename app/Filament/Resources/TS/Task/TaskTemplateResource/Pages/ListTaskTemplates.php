<?php

namespace App\Filament\Resources\TS\Task\TaskTemplateResource\Pages;

use App\Filament\Resources\TS\Task\TaskTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTaskTemplates extends ListRecords
{
    protected static string $resource = TaskTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
