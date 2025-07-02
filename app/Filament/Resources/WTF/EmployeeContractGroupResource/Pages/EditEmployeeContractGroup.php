<?php

namespace App\Filament\Resources\WTF\EmployeeContractGroupResource\Pages;

use App\Filament\Resources\WTF\EmployeeContractGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmployeeContractGroup extends EditRecord
{
    protected static string $resource = EmployeeContractGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
