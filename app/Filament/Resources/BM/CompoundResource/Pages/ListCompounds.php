<?php

namespace App\Filament\Resources\BM\CompoundResource\Pages;

use App\Filament\Resources\BM\CompoundResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCompounds extends ListRecords
{
    protected static string $resource = CompoundResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
