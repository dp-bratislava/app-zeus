<?php

namespace App\Filament\Resources\BM\OfficeResource\Pages;

use App\Filament\Resources\BM\OfficeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOffices extends ListRecords
{
    protected static string $resource = OfficeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
