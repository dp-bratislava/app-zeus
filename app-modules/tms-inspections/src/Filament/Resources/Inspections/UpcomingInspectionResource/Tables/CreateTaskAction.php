<?php

namespace Dpb\Modules\Inspections\Filament\Resources\Inspections\UpcomingInspectionResource\Tables;

use Dpb\Package\TaskMS\Models\InspectionAssignment;
use Dpb\Package\TaskMS\Models\TicketAssignment;
use Dpb\Package\TaskMS\UseCases\TicketAssignment\CreateFromInspectionUseCase;
use Filament\Actions\Action;

class CreateTaskAction
{
    // public static function make($uri): Action
    // {
    //     return Action::make($uri)
    //         ->label(__('dpb-mod-inspections::upcoming-inspection.table.actions.create_ticket'))
    //         ->button()
    //         // ->action(function (InspectionAssignment $record, TicketAssignmentRepository $ticketAssignmentRepository) {
    //         //     $ticketAssignmentRepository->createFromInspectionAssignment($record);
    //         // })
    //         ->action(function (InspectionAssignment $record, CreateFromInspectionUseCase $createFromInspectionUseCase) {
    //             $createFromInspectionUseCase->execute($record);
    //         })
    //         ->visible(function (InspectionAssignment $record, TicketAssignment $ticketAssignment) {
    //             // return true;
    //             // return $ticketAssignment->whereHasMorph($record, $record->getMorphClass());
    //             return !TicketAssignment::where('source_type', $record->inspection->getMorphClass())
    //                 ->where('source_id', $record->id)
    //                 ->exists();
    //         });
    // }
}
