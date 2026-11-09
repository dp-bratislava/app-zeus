<?php

namespace Dpb\Modules\Inspections\Filament\Resources\InspectionAssignments\Mappers;

use Dpb\Package\TaskMS\Commands\Inspection\UpdateInspectionCommand;
use Dpb\Package\TaskMS\Commands\InspectionAssignment\UpdateInspectionAssignmentCommand;
use Dpb\Package\TaskMS\Models\InspectionAssignment;
use Dpb\Package\TaskMS\Resolvers\InspectionSubjectResolver;
use Dpb\Package\TaskMS\States;

class InspectionUpdateFormMapper
{
    public function __construct(
        private InspectionSubjectResolver $inspectionSubjectResolver
    ) {}

    public function fromForm(InspectionAssignment $record, array $data): array
    {
        // create inspection
        $inspectionCommand = new UpdateInspectionCommand(
            $record->inspection->id,
            new \DateTimeImmutable($data['date']),
            $data['template_id'] ?? null,
            States\Inspection\Upcoming::$name,
        );

        // create inspection assignment
        $subject = $this->inspectionSubjectResolver->resolveByMorph('vehicle', $data['subject_id']);
        $inspectionAssignmentCommand = new UpdateInspectionAssignmentCommand(
            $record->id,
            $record->inspection->id,
            $subject->id,
            $subject->morphClass,
        );

        return [
            'inspectionCommand' => $inspectionCommand,
            'inspectionAssignmentCommand' => $inspectionAssignmentCommand,
        ];
    }
}
