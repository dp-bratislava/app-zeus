<?php

namespace Dpb\Modules\Inspections\Filament\Resources\Inspections\InspectionTemplateGroupResource\Pages;

use Dpb\Modules\Inspections\Filament\Resources\Inspections\InspectionTemplateGroupResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateInspectionTemplateGroup extends CreateRecord
{
    protected static string $resource = InspectionTemplateGroupResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('dpb-mod-inspections::inspection-template-group.create_heading');
    }
}
