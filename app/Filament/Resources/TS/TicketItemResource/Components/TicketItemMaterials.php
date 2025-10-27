<?php

namespace App\Filament\Resources\TS\TicketItemResource\Components;

use App\Services\TS\MaterialService;
use Dpb\Package\Tickets\Models\TicketItem;
use Livewire\Component;

class TicketItemMaterials extends Component
{
    // public TicketItem $ticketItem;
    public $materials;
    public $totalMaterialExpenses;

    public function mount(
        TicketItem $ticketItem,
        MaterialService $materialSvc
    ) {
        // expense materials
        $this->materials = $materialSvc->getMaterials($ticketItem->ticket);
        $this->totalMaterialExpenses = $materialSvc->getTotalMaterialExpenses($ticketItem->ticket);
    }

    public function render()
    {
        return view('filament.resources.ts.ticket-item-resource.components.ticket-item-materials');
    }
}
