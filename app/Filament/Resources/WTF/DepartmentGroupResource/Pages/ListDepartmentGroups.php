<?php

namespace App\Filament\Resources\WTF\DepartmentGroupResource\Pages;

use App\Filament\Resources\WTF\DepartmentGroupResource;
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
