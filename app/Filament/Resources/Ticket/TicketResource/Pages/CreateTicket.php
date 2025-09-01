<?php

namespace App\Filament\Resources\Ticket\TicketResource\Pages;

use App\Filament\Resources\Ticket\TicketResource;
use App\Services\TicketService;
use Dpb\DatahubSync\Models\Department;
use Filament\Resources\Pages\CreateRecord;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    protected function afterSave(): void
    {
        $data = $this->form->getState();
        $department = Department::findOrFail($data['department_id']);

        app(TicketService::class)->assignDepartment($this->record, $department);
    }
}
