<?php

namespace App\Filament\Resources\Ticket\TicketResource\Pages;

use App\Filament\Resources\Ticket\TicketResource;
use App\Services\TicketService;
use Dpb\DatahubSync\Models\Department;
use Dpb\Packages\Vehicles\Models\Vehicle;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // protected function beforeFill() {

    // }

    // protected function afterSave(): void
    // {
    //     $data = $this->form->getState();
    //     // dd($data);
    //     $department = Department::findOrFail($data['department_id']);

    //     app(TicketService::class)->assignDepartment($this->record, $department);

    //     $vehicle = Vehicle::findOrFail($data['vehicle_id']);

    //     app(TicketService::class)->assignVehicle($this->record, $vehicle);
    // }

    // protected function mutateFormDataBeforeFill(array $data): array
    // {
    //     $departmentId = app(TicketService::class)->getDepartment($this->record)?->id;
    //     $vehicleId = app(TicketService::class)->getVehicle($this->record)?->id;

    //     $data['department_id'] = $departmentId;
    //     $data['vehicle_id'] = $vehicleId;

    //     return $data;
    // }
}
