<?php

namespace Dpb\Modules\Inspections\Filament\Resources\InspectionAssignments\Mappers;

use Dpb\Package\TaskMS\Commands\Inspection\CreateInspectionCommand;
use Dpb\Package\TaskMS\Commands\InspectionAssignment\CreateInspectionAssignmentCommand;
use Dpb\Package\TaskMS\States;

class InspectionCreateFormMapper
{
    public function fromForm(array $data): array
    {
        // create inspection
        $inspectionCommand = new CreateInspectionCommand(
            new \DateTimeImmutable($data['date']),
            $data['template_id'] ?? null,
            States\Inspection\Upcoming::$name,
        );

        // create inspection templatables
        $inspectionAssignmentCommand = new CreateInspectionAssignmentCommand(
            null,
            $data['subject_id'],
            'vehicle',
        );

        return [
            'inspectionCommand' => $inspectionCommand,
            'inspectionAssignmentCommand' => $inspectionAssignmentCommand,
        ];
    }
}
