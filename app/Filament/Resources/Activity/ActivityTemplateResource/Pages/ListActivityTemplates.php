<?php

namespace App\Filament\Resources\Activity\ActivityTemplateResource\Pages;

use App\Filament\Resources\Activity\ActivityTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListActivityTemplates extends ListRecords
{
    protected static string $resource = ActivityTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
