<?php

namespace App\Filament\Resources\TS\Issue\TypeResource\Pages;

use App\Filament\Resources\TS\Issue\TypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditType extends EditRecord
{
    protected static string $resource = TypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
