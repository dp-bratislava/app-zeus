<?php

namespace App\Services\TS;

use App\Models\Expense\Material as ExpenseMaterial;
use Dpb\Package\Tickets\Models\Ticket;
use Illuminate\Database\Eloquent\Collection;

class MaterialService
{
    public function __construct(protected ExpenseMaterial $material) {}

    // public function assignUnitRate(ActivityTemplate $tempalte, UnitRate $unitRate)
    // {
    //     $this->erService->createRelation($ticket, $vehicle, 'assigned');
    // }

    public function getMaterials(Ticket $ticket): Collection
    {        
        return $this->material            
            ->where('ticket_id', '=', $ticket->id)
            ->get();
    }

    public function getTotalMaterialExpenses(Ticket $ticket)
    {        
        return $this->material            
            ->where('ticket_id', '=', $ticket->id)
            ->get()
            ->sum('price');
    }
}
