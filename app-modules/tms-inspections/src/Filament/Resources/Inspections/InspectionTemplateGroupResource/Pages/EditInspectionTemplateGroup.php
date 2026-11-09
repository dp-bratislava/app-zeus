<?php

namespace Dpb\Modules\Inspections\Filament\Resources\Inspections\InspectionTemplateGroupResource\Pages;

use Dpb\Modules\Inspections\Filament\Resources\Inspections\InspectionTemplateGroupResource;
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

    public function getTitle(): string|Htmlable
    {
        return __('dpb-mod-inspections::inspection-template-group.update_heading', ['title' => $this->record->title]);
    }
}
