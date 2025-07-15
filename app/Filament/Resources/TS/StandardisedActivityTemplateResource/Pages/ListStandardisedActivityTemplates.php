<?php

namespace App\Filament\Resources\TS\StandardisedActivityTemplateResource\Pages;

use App\Filament\Resources\TS\StandardisedActivityTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStandardisedActivityTemplates extends ListRecords
{
    protected static string $resource = StandardisedActivityTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
