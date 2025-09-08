<?php

namespace App\Filament\Resources\Ticket\TicketResource\Pages;

use App\Filament\Resources\Ticket\TicketResource;
use App\Services\TicketService;
use Dpb\DatahubSync\Models\Department;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                // ->after(function (TicketService $ticketService, Department $departmentHdl) {
                //     $data = $this->form->getState();
                //     $department = $departmentHdl->findOrFail($data['department_id']);

                //     $ticketService->assignDepartment($this->record, $department);
                // }),
        ];
    }
}
