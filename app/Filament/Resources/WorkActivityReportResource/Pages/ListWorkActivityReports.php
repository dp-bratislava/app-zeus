<?php

namespace App\Filament\Resources\WorkActivityReportResource\Pages;

use App\Filament\Resources\WorkActivityReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListWorkActivityReports extends ListRecords
{
    protected static string $resource = WorkActivityReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    public function getTitle(): string | Htmlable
    {
        return '';
    }     
}
