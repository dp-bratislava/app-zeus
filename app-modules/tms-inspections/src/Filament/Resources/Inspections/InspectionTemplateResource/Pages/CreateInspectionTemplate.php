<?php

namespace Dpb\Modules\Inspections\Filament\Resources\Inspections\InspectionTemplateResource\Pages;

use Dpb\Modules\Inspections\Filament\Resources\Inspections\InspectionTemplateResource;
use Dpb\Modules\Inspections\Mappers\Inspection\InspectionTemplateCreateFormMapper;
use Dpb\Package\TaskMS\Workflows\CreateInspectionTemplateWorkflow;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class CreateInspectionTemplate extends CreateRecord
{
    protected static string $resource = InspectionTemplateResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('dpb-mod-inspections::inspection-template.create_heading');
    }

    public function handleRecordCreation(array $data): Model
    {
        $command = app(InspectionTemplateCreateFormMapper::class)->fromForm($data);

        return app(CreateInspectionTemplateWorkflow::class)->handle($command);
    }
}
