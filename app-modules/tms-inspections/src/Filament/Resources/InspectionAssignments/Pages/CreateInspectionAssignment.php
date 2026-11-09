<?php

namespace Dpb\Modules\Inspections\Filament\Resources\InspectionAssignments\Pages;

use Dpb\Modules\Inspections\Filament\Resources\InspectionAssignmentResource;
use Dpb\Modules\Inspections\Filament\Resources\InspectionAssignments\Mappers\InspectionCreateFormMapper;
use Dpb\Package\TaskMS\Workflows\CreateInspectionWorkflow;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class CreateInspectionAssignment extends CreateRecord
{
    protected static string $resource = InspectionAssignmentResource::class;

    public function getTitle(): string | Htmlable
    {
        return __('dpb-mod-inspections::inspection-assignment.create_heading');
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        $commands = app(InspectionCreateFormMapper::class)->fromForm($data);
        return app(CreateInspectionWorkflow::class)->handle(
            $commands['inspectionCommand'],
            $commands['inspectionAssignmentCommand'],
        );
    }
}
