<?php

namespace App\Filament\Resources\Inspection\InspectionTemplateResource\Pages;

use App\Filament\Resources\Inspection\InspectionTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListInspectionTemplates extends ListRecords
{
    protected static string $resource = InspectionTemplateResource::class;

    
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
