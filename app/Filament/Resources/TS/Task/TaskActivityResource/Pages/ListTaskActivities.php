<?php

namespace App\Filament\Resources\TS\Task\TaskActivityResource\Pages;

use App\Filament\Resources\TS\Task\TaskActivityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTaskActivities extends ListRecords
{
    protected static string $resource = TaskActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
