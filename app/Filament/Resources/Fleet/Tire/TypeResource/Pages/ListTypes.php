<?php

namespace App\Filament\Resources\Fleet\Tire\TypeResource\Pages;

use App\Filament\Resources\Fleet\Tire\TypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTypes extends ListRecords
{
    protected static string $resource = TypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
