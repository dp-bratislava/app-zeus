<?php

namespace App\Filament\Resources\DispatchReportResource\Pages;

use App\Filament\Resources\DispatchReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDispatchReport extends EditRecord
{
    protected static string $resource = DispatchReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
