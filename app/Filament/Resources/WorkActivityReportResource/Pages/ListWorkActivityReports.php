<?php

namespace App\Filament\Resources\WorkActivityReportResource\Pages;

use App\Filament\Resources\WorkActivityReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWorkActivityReports extends ListRecords
{
    protected static string $resource = WorkActivityReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
