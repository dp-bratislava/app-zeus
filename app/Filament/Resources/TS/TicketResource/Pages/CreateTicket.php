<?php

namespace App\Filament\Resources\TS\TicketResource\Pages;

use App\Filament\Resources\TS\TicketResource;
use App\Services\TicketAssignmentRepository;
use App\Services\TicketService;
use Dpb\DatahubSync\Models\Department;
use Dpb\Package\Vehicles\Models\Vehicle;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    public function getTitle(): string | Htmlable
    {
        return __('tickets/ticket.create_heading');
    } 

    protected function handleRecordCreation(array $data): Model
    {
        // dd($data);
        $ticketAssignmentRepo = app(TicketAssignmentRepository::class);
        return $ticketAssignmentRepo->create($data);
    } 
}
