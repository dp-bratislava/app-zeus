<?php

namespace App\Filament\Resources\WTF\StandardisedActivityTemplateResource\Pages;

use App\Filament\Resources\WTF\StandardisedActivityTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStandardisedActivityTemplate extends EditRecord
{
    protected static string $resource = StandardisedActivityTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
