<?php

namespace App\Filament\Resources\Ticket\TicketResource\Pages;

use App\Filament\Resources\Ticket\TicketResource;
use App\Services\TicketService;
use Dpb\DatahubSync\Models\Department;
use Dpb\Package\Vehicles\Models\Vehicle;
use Filament\Resources\Pages\CreateRecord;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    // protected function afterCreate(): void
    // {
    //     $data = $this->form->getState();
    //     $department = Department::findOrFail($data['department_id']);

    //     app(TicketService::class)->assignDepartment($this->record, $department);

    //     $vehicle = Vehicle::findOrFail($data['vehicle_id']);

    //     app(TicketService::class)->assignVehicle($this->record, $vehicle);
    // }
}
