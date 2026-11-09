<?php

namespace Dpb\Modules\Inspections\Filament\Resources\InspectionAssignments\Actions;

use Dpb\Package\TaskMS\Models\InspectionAssignment;
use Dpb\Package\TaskMS\Models\TaskAssignment;
use Dpb\Package\TaskMS\Workflows\CreateTaskFromInspectionWorkflow;
use Dpb\WtfTmsBridge\Filament\Resources\TaskAssignmentResource;
use Filament\Actions\Action;

class CreateTaskAction
{
    public static function make($uri): Action
    {
        return Action::make($uri)
            ->label(__('dpb-mod-inspections::inspection-assignment.table.actions.create_task'))
            ->button()
            ->action(function (InspectionAssignment $record, CreateTaskFromInspectionWorkflow $workflow) {
                // create task asignement
                $taskAssignment = $workflow->createFromInspectionAssignment($record);

                // redirect to edit form for newly created task assignment
                return redirect(
                    TaskAssignmentResource::getUrl('edit', [
                        'record' => $taskAssignment
                    ])
                );
            })
            ->visible(function (InspectionAssignment $record) {
                // return true;
                // return $ticketAssignment->whereHasMorph($record, $record->getMorphClass());
                // task exists
                $taskAlreadyExists = TaskAssignment::where('source_type', $record->inspection->getMorphClass())
                    ->where('source_id', $record->id)
                    ->exists();

                // inspection is daily maintenance, which creates tasks autoamticaly 
                // but puts all selected templates into one task so there are inconsitencies 
                // since each tempalte is stored as separate inspction therefore has no task
                // $isDailyMaintenance = $record->inspection->template->groups->first()->code == 'daily-maintenance';
                $isDailyMaintenance = false;

                return (!($isDailyMaintenance || $taskAlreadyExists));
                // return !TaskAssignment::where('source_type', $record->inspection->getMorphClass())
                //     ->where('source_id', $record->id)
                //     ->exists();
            });
    }
}
