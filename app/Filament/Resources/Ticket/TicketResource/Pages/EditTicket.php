<?php

namespace App\Filament\Resources\Ticket\TicketResource\Pages;

use App\Filament\Resources\Ticket\TicketResource;
use App\Services\TicketService;
use Dpb\DatahubSync\Models\Department;
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

    protected function afterSave(): void
    {
        $data = $this->form->getState();
        // dd($data);
        $department = Department::findOrFail($data['department_id']);

        app(TicketService::class)->assignDepartment($this->record, $department);
    }

}
