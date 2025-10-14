<?php

namespace App\Services\Ticket;

use App\Models\Expense\Service as ExpenseService;
use Dpb\Package\Tickets\Models\Ticket;
use Illuminate\Database\Eloquent\Collection;

class ServiceService
{
    public function __construct(protected ExpenseService $service) {}

    // public function assignUnitRate(ActivityTemplate $tempalte, UnitRate $unitRate)
    // {
    //     $this->erService->createRelation($ticket, $vehicle, 'assigned');
    // }

    public function getServices(Ticket $ticket): Collection
    {        
        return $this->service
            ->where('ticket_id', '=', $ticket->id)
            ->get();
    }

    public function getTotalServiceExpenses(Ticket $ticket)
    {        
        return $this->service            
            ->where('ticket_id', '=', $ticket->id)
            ->get()
            ->sum('price');
    }    
}
