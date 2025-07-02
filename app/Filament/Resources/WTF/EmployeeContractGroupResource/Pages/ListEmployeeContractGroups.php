<?php

namespace App\Filament\Resources\WTF\EmployeeContractGroupResource\Pages;

use App\Filament\Resources\WTF\EmployeeContractGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmployeeContractGroups extends ListRecords
{
    protected static string $resource = EmployeeContractGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
