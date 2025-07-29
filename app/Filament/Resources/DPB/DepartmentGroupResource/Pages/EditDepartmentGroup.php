<?php

namespace App\Filament\Resources\DPB\DepartmentGroupResource\Pages;

use App\Filament\Resources\DPB\DepartmentGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDepartmentGroup extends EditRecord
{
    protected static string $resource = DepartmentGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
