<?php

namespace App\Filament\Resources\Incident\IncidentResource\Tables;

use App\Models\IncidentAssignment;
use App\Models\TicketAssignment;
use App\Services\TicketAssignmentRepository;
use App\Services\TS\TicketAssignmentService;
use Dpb\Package\Incidents\Models\Incident;
use Filament\Tables\Actions\Action;

class CreateTicketAction
{
    public static function make($uri): Action
    {
        return Action::make($uri)
            ->label(__('incidents/incident.table.actions.create_ticket'))
            ->button()
            ->action(function (IncidentAssignment $record, TicketAssignmentRepository $ticketAssignmentRepository) {
                $ticketAssignmentRepository->createFromIncidentAssignment($record);
            })
            ->visible(function (IncidentAssignment $record, TicketAssignment $ticketAssignment) {
                // return true;
                // return $ticketAssignment->whereHasMorph($record, $record->getMorphClass());
                return !TicketAssignment::where('source_type', $record->incident->getMorphClass())
                    ->where('source_id', $record->id)
                    ->exists();
            });
    }
}
