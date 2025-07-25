<?php

namespace App\Filament\Resources\TS\Task\TemplateGroupResource\Pages;

use App\Filament\Resources\TS\Task\TemplateGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTemplateGroup extends EditRecord
{
    protected static string $resource = TemplateGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
