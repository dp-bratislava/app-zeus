<?php

namespace Dpb\Modules\Inspections\Filament\Resources\InspectionAssignments\Actions;

use Dpb\Package\TaskMS\Models\InspectionAssignment;
use Dpb\Package\TaskMS\Models\TaskAssignment;
use Dpb\Package\TaskMS\Workflows\CreateTaskFromInspectionWorkflow;
use Dpb\WtfTmsBridge\Filament\Resources\TaskAssignmentResource;
use Filament\Notifications\Notification;
use Filament\Actions\Action;


class EditTaskAction
{
    public static function make($uri): Action
    {
        return Action::make($uri)
            ->label(__('dpb-mod-inspections::inspection-assignment.table.actions.edit_task'))
            ->button()
            ->color('success')
            ->action(function (InspectionAssignment $record, CreateTaskFromInspectionWorkflow $svc) {
                // get task assignment created from this inspection
                // $taskAssignmentId = TaskAssignment::whereMorphedTo('source', $record->inspection)
                //     ->first()?->id;
                $taskAssignment = TaskAssignment::whereMorphedTo('source', $record->inspection)
                    ->first();

                // handle nonexistent task error
                if (! $taskAssignment) {
                    Notification::make()
                        ->title(__('dpb-mod-inspections::inspection-assignment.table.notifications.task_not_found'))
                        ->danger()
                        ->send();

                    return;
                }

                // redirect to edit form for task assignment
                return redirect(
                    TaskAssignmentResource::getUrl('edit', [
                        'record' => $taskAssignment->getKey()
                    ])
                );
            })
            ->visible(function (InspectionAssignment $record) {
                // task exists
                return TaskAssignment::where('source_type', '=', $record->inspection->getMorphClass())
                    ->where('source_id', '=', $record->inspection->id)
                    ->exists();
            });
    }
}
