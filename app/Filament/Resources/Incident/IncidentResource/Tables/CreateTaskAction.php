<?php

namespace App\Filament\Resources\Incident\IncidentResource\Tables;

use App\Application\Tasks\CreateTaskFromIncidentUesCase;
use App\Models\IncidentAssignment;
use Filament\Tables\Actions\Action;

class CreateTaskAction
{
    public static function make($uri): Action
    {
        return Action::make($uri)
            ->label(__('incidents/incident.table.actions.create_ticket'))
            ->button()
            ->action(function (IncidentAssignment $record, CreateTaskFromIncidentUesCase $useCase) {
                $useCase->execute($record);
            });
    }
}
