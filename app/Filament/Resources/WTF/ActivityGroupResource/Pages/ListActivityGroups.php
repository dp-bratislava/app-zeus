<?php

namespace App\Filament\Resources\WTF\ActivityGroupResource\Pages;

use App\Filament\Resources\WTF\ActivityGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListActivityGroups extends ListRecords
{
    protected static string $resource = ActivityGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
