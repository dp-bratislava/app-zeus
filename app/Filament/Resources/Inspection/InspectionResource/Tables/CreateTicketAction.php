<?php

namespace App\Filament\Resources\Inspection\InspectionResource\Tables;

use App\Models\InspectionAssignment;
use App\Models\TicketAssignment;
use App\UseCases\TicketAssignment\CreateFromInspectionUseCase;
use Filament\Tables\Actions\Action;

class CreateTicketAction
{
    public static function make($uri): Action
    {
        return Action::make($uri)
            ->label(__('inspections/inspection.table.actions.create_ticket'))
            ->button()
            // ->action(function (InspectionAssignment $record, TicketAssignmentRepository $ticketAssignmentRepository) {
            //     $ticketAssignmentRepository->createFromInspectionAssignment($record);
            // })
            ->action(function (InspectionAssignment $record, CreateFromInspectionUseCase $createFromInspectionUseCase) {
                $createFromInspectionUseCase->execute($record);
            })
            ->visible(function (InspectionAssignment $record, TicketAssignment $ticketAssignment) {
                // return true;
                // return $ticketAssignment->whereHasMorph($record, $record->getMorphClass());
                return !TicketAssignment::where('source_type', $record->inspection->getMorphClass())
                    ->where('source_id', $record->id)
                    ->exists();
            });
    }
}
