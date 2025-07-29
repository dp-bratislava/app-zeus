<?php

namespace App\Filament\Resources\DPB\DepartmentGroupResource\Pages;

use App\Filament\Resources\DPB\DepartmentGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDepartmentGroups extends ListRecords
{
    protected static string $resource = DepartmentGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
