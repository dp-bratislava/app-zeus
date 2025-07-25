<?php

namespace App\Filament\Resources\TS\Task\TemplateGroupResource\Pages;

use App\Filament\Resources\TS\Task\TemplateGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTemplateGroups extends ListRecords
{
    protected static string $resource = TemplateGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
