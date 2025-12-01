<?php

namespace App\Filament\Resources\Inspection\InspectionTemplateGroupResource\Pages;

use App\Filament\Resources\Inspection\InspectionTemplateGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditInspectionTemplateGroup extends EditRecord
{
    protected static string $resource = InspectionTemplateGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function getTitle(): string | Htmlable
    {
        return __('inspections/inspection-template-group.update_heading', ['title' => $this->record->title]);
    }

}
