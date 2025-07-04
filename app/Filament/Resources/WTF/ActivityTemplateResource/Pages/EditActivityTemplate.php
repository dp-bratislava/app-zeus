<?php

namespace App\Filament\Resources\WTF\ActivityTemplateResource\Pages;

use App\Filament\Resources\WTF\ActivityTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditActivityTemplate extends EditRecord
{
    protected static string $resource = ActivityTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
